<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\SupplierCatalogModel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use ZipArchive;

class ProdukController extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $supplierModel;
    protected $catalogModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new SupplierModel();
        $this->catalogModel = new SupplierCatalogModel();
        
        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk'; 
        if (!is_dir($uploadPath)) {
            @mkdir($uploadPath, 0777, true); 
        }
    }

    public function index()
    {
        $filterKategori = $this->request->getGet('kategori');
        $query = $this->produkModel
            ->select('produk.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->orderBy('produk.nama_produk', 'ASC');

        if (!empty($filterKategori)) {
            $query->where('produk.id_kategori', $filterKategori);
        }

        $produkData = $query->findAll();
        $kategoriList = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        $data = [
            'produk'           => $produkData,
            'kategori'         => $kategoriList,
            'selectedKategori' => $filterKategori,
            'title'            => 'Manajemen Produk'
        ];
        
        return view('admin/produk/index', $data);
    }

    public function new()
    {
         $data = [
            'title'      => 'Tambah Produk Baru',
            'kategori'   => $this->kategoriModel->findAll(),
            'suppliers'  => $this->supplierModel->select('id, nama_supplier, id_kategori')->findAll(), 
            'validation' => \Config\Services::validation()
        ];
        return view('admin/produk/new', $data);
    }

    private function generateBarcode(int $id_kategori): string
    {
        $companyCode = '400';
        $categoryCode = str_pad($id_kategori, 3, '0', STR_PAD_LEFT);
        $prefix = $companyCode . $categoryCode;

        $lastBarcode = $this->produkModel
                            ->selectMax('barcode')
                            ->where('id_kategori', $id_kategori)
                            ->like('barcode', $prefix . '%', 'after')
                            ->get()
                            ->getRow()
                            ->barcode;

        $newProductNum = 1;
        if ($lastBarcode) {
            try {
                $lastProductSeq = (int) substr($lastBarcode, 6); 
                $newProductNum = $lastProductSeq + 1;
            } catch (\Exception $e) { $newProductNum = 1; }
        }
        return $prefix . str_pad($newProductNum, 3, '0', STR_PAD_LEFT);
    }

    // [MODIFIKASI UTAMA] Create Produk & Integrasi Katalog
    public function create()
    {
        // 1. Validasi Input
        $rules = [
            'nama_produk' => 'required|min_length[3]|is_unique[produk.nama_produk]',
            'id_kategori' => 'required|integer|is_not_unique[kategori.id]',
            'id_supplier' => 'required|integer|is_not_unique[suppliers.id]',
            'harga'       => 'required|numeric|greater_than[0]', // Harga Jual
            'harga_beli'  => 'required|numeric|greater_than_equal_to[0]', // Harga Beli (dari Supplier)
            // Stok tidak divalidasi dari input karena default 0
            'exp_date'    => 'permit_empty|valid_date[Y-m-d]',
            'posisi_rak'  => 'permit_empty|max_length[50]',
            'foto_produk' => [
                'label' => 'Foto Produk',
                'rules' => 'uploaded[foto_produk]|is_image[foto_produk]|mime_in[foto_produk,image/jpg,image/jpeg,image/png]|max_size[foto_produk,2048]',
            ],
        ];

        if (!$this->validate($rules)) {
            $post = $this->request->getPost();
            unset($post['foto_produk']); 
            return redirect()->back()->withInput($post)->with('validation', $this->validator);
        }

        $id_kategori = (int) $this->request->getVar('id_kategori');
        $newBarcode = $this->generateBarcode($id_kategori);
        
        // 2. Upload Foto
        $fotoFile = $this->request->getFile('foto_produk');
        $fotoName = null;
        if ($fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $fotoName = $fotoFile->getRandomName();
            $fotoFile->move(FCPATH . 'uploads/produk', $fotoName);
        }

        // 3. Simpan Produk (STOK AWAL 0 sesuai permintaan)
        $this->produkModel->save([
            'nama_produk' => $this->request->getVar('nama_produk'),
            'id_kategori' => $id_kategori,
            'barcode'     => $newBarcode,
            'foto_produk' => $fotoName,
            'harga'       => $this->request->getVar('harga'),      // Harga Jual
            'harga_beli'  => $this->request->getVar('harga_beli'), // Harga Beli
            'stok'        => 0,                                    // Stok Kosong Dulu
            'exp_date'    => $this->request->getVar('exp_date') ?: null,
            'posisi_rak'  => $this->request->getVar('posisi_rak') ?: null,
        ]);

        $newProductId = $this->produkModel->insertID();

        // 4. INTEGRASI: Hubungkan dengan Katalog Supplier
        // Cek apakah user memilih item dari katalog yang sudah ada?
        $idCatalogItem = $this->request->getVar('id_catalog_item');
        
        if (!empty($idCatalogItem)) {
            // Skenario A: User memilih item katalog "mentah" dari dropdown
            // Kita update item tersebut agar terhubung ke produk baru ini (id_produk terisi)
            $this->catalogModel->update($idCatalogItem, [
                'id_produk' => $newProductId,
                // Optional: Update harga di katalog jika user mengubah harga beli di form
                'harga_dari_supplier' => $this->request->getVar('harga_beli') 
            ]);
        } else {
            // Skenario B: User input produk manual (tidak pilih dari katalog)
            // Kita buatkan entry baru di katalog supplier
            $this->catalogModel->save([
                'id_supplier'         => $this->request->getVar('id_supplier'),
                'id_produk'           => $newProductId,
                'nama_item'           => $this->request->getVar('nama_produk'), // Nama item di supplier = nama produk
                'harga_dari_supplier' => $this->request->getVar('harga_beli'),
                'stok_tersedia'       => 0, // Default 0
                'exp_date'            => $this->request->getVar('exp_date') ?: null,
            ]);
        }

        return redirect()->to('/admin/produk')->with('success', 'Produk berhasil dibuat dan terintegrasi dengan Supplier.' );
    }

    public function edit($id = null)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');

        $barcodeImageBase64 = null;
        if (!empty($produk['barcode'])) {
            try {
                $generator = new BarcodeGeneratorPNG();
                $pngData = $generator->getBarcode($produk['barcode'], $generator::TYPE_CODE_128, 2, 60);
                $barcodeImageBase64 = 'data:image/png;base64,' . base64_encode($pngData);
            } catch (\Exception $e) { $barcodeImageBase64 = null; }
        }

        $fotoUrl = null;
        if ($produk['foto_produk']) {
            $filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk' . DIRECTORY_SEPARATOR . $produk['foto_produk'];
            if (file_exists($filePath)) $fotoUrl = base_url('uploads/produk/' . $produk['foto_produk']);
        }

        $data = [
            'title'        => 'Edit Produk',
            'produk'       => $produk,
            'kategori'     => $this->kategoriModel->findAll(),
            'barcodeImage' => $barcodeImageBase64, 
            'fotoUrl'      => $fotoUrl,
            'validation'   => \Config\Services::validation()
        ];
        return view('admin/produk/edit', $data);
    }
    
    public function update($id = null)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');

        $namaProdukRule = ($this->request->getVar('nama_produk') != $produk['nama_produk']) ? '|is_unique[produk.nama_produk,id,' . $id . ']' : '';
        $barcodeRule = ($this->request->getVar('barcode') != $produk['barcode']) ? '|is_unique[produk.barcode,id,' . $id . ']' : '';

        $rules = [
            'nama_produk' => 'required|min_length[3]' . $namaProdukRule,
            'id_kategori' => 'required|integer|is_not_unique[kategori.id]',
            'barcode'     => 'permit_empty|alpha_numeric' . $barcodeRule, 
            'harga'       => 'required|numeric|greater_than[0]',
            'harga_beli'  => 'required|numeric|greater_than_equal_to[0]',
            // Stok tidak diupdate manual di sini (sesuai permintaan)
            'exp_date'    => 'permit_empty|valid_date[Y-m-d]',
            'posisi_rak'  => 'permit_empty|max_length[50]',
            'foto_produk' => [
                'label' => 'Foto Produk',
                'rules' => 'permit_empty|is_image[foto_produk]|mime_in[foto_produk,image/jpg,image/jpeg,image/png]|max_size[foto_produk,2048]',
            ],
        ];

        if (!$this->validate($rules)) {
            $post = $this->request->getPost();
            unset($post['foto_produk']); 
            return redirect()->back()->withInput($post)->with('validation', $this->validator);
        }
        
        $dataToUpdate = [
            'nama_produk' => $this->request->getVar('nama_produk'),
            'id_kategori' => $this->request->getVar('id_kategori'),
            'barcode'     => $this->request->getVar('barcode') ?: null,
            'harga'       => $this->request->getVar('harga'),
            'harga_beli'  => $this->request->getVar('harga_beli'),
            // 'stok' => TIDAK DIUPDATE DI SINI
            'exp_date'    => $this->request->getVar('exp_date') ?: null,
            'posisi_rak'  => $this->request->getVar('posisi_rak') ?: null,
        ];
        
        $fotoFile = $this->request->getFile('foto_produk');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $oldFoto = $produk['foto_produk'];
            $oldFilePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk' . DIRECTORY_SEPARATOR . $oldFoto;
            if ($oldFoto && file_exists($oldFilePath)) unlink($oldFilePath);

            $newName = $fotoFile->getRandomName();
            $fotoFile->move(FCPATH . 'uploads/produk', $newName);
            $dataToUpdate['foto_produk'] = $newName;
        }

        if ($this->produkModel->skipValidation(true)->update($id, $dataToUpdate)) {
            log_activity('update_product', 'Admin memperbarui produk: ' . $this->request->getVar('nama_produk') . ' (ID: ' . $id . ')');
            return redirect()->to('/admin/produk')->with('success', 'Produk berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan perubahan ke database.');
        }
    }

    public function delete($id = null)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');

        if (!empty($produk['foto_produk'])) {
             $filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk' . DIRECTORY_SEPARATOR . $produk['foto_produk'];
            if (file_exists($filePath)) unlink($filePath);
        }

        $this->produkModel->delete($id);
        return redirect()->to('/admin/produk')->with('success', 'Produk berhasil dihapus.');
    }

    public function show($id = null)
    {
        $produk = $this->produkModel
                        ->select('produk.*, kategori.nama_kategori')
                        ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                        ->find($id);
        if (!$produk) return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');

        $barcodeImageBase64 = null;
        if (!empty($produk['barcode'])) {
            try {
                $generator = new BarcodeGeneratorPNG();
                $pngData = $generator->getBarcode($produk['barcode'], $generator::TYPE_CODE_128, 2, 60);
                $barcodeImageBase64 = 'data:image/png;base64,' . base64_encode($pngData);
            } catch (\Exception $e) { $barcodeImageBase64 = null; }
        }
        
        $fotoUrl = null;
        if ($produk['foto_produk']) {
            $filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk' . DIRECTORY_SEPARATOR . $produk['foto_produk'];
            if (file_exists($filePath)) $fotoUrl = base_url('uploads/produk/' . $produk['foto_produk']);
        }

        $data = [
            'title'        => 'Detail Produk',
            'produk'       => $produk,
            'barcodeImage' => $barcodeImageBase64,
            'fotoUrl'      => $fotoUrl,
        ];
        return view('admin/produk/show', $data);
    }

    public function downloadAllBarcodes()
    {
        set_time_limit(0); 
        if (!class_exists('ZipArchive')) return redirect()->to('/admin/produk')->with('error', 'Gagal: Ekstensi PHP ZipArchive tidak terinstal.');
        
        $produk = $this->produkModel->where('barcode IS NOT NULL')->findAll();
        if (empty($produk)) return redirect()->to('/admin/produk')->with('error', 'Tidak ada produk dengan barcode untuk diunduh.');

        $zip = new ZipArchive();
        $zipFileName = 'barcodes_' . date('YmdHis') . '.zip';
        $zipFilePath = WRITEPATH . 'cache/' . $zipFileName;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return redirect()->to('/admin/produk')->with('error', 'Gagal membuat file ZIP.');

        $generator = new BarcodeGeneratorPNG();
        foreach ($produk as $p) {
            if (!empty($p['barcode'])) {
                try {
                    $pngData = $generator->getBarcode($p['barcode'], $generator::TYPE_CODE_128, 2, 60);
                    $fileName = $p['barcode'] . '_' . str_replace([' ', '/', '\\', ':', '*','?','"','<','>','|'], '_', $p['nama_produk']) . '.png';
                    $zip->addFromString($fileName, $pngData);
                } catch (\Exception $e) {}
            }
        }
        $zip->close();
        
        if (!file_exists($zipFilePath)) return redirect()->to('/admin/produk')->with('error', 'File ZIP tidak ditemukan.');
        return $this->response->download($zipFilePath, null, true)->setFileName($zipFileName)->send();
    }

    // [API BARU] Mengambil item katalog "mentah" (belum terhubung ke produk) dari supplier
    // Digunakan di form "Tambah Produk" untuk auto-fill data
    public function getCatalogItemsBySupplier($idSupplier)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        // Ambil item yang id_produk-nya NULL (belum jadi produk resmi)
        $items = $this->catalogModel
                      ->where('id_supplier', $idSupplier)
                      ->where('id_produk', NULL) 
                      ->findAll();

        return $this->response->setJSON($items);
    }
}