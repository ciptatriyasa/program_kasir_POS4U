<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\ProdukModel;
use Config\Services;

class KategoriController extends BaseController
{
    protected $kategoriModel;
    protected $produkModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->produkModel   = new ProdukModel();
    }

    /**
     * Menampilkan daftar kategori
     */
    public function index()
    {
        try {
            // 1. Ambil semua kategori
            $kategori = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

            // 2. Ambil semua produk
            $allProduk = $this->produkModel->orderBy('nama_produk', 'ASC')->findAll();

            // 3. Kelompokkan produk berdasarkan id_kategori
            $produkByKategori = [];
            
            if (!empty($allProduk)) {
                foreach ($allProduk as $produk) {
                    // Pastikan key 'id_kategori' sesuai dengan nama kolom di tabel produk Anda
                    $idKat = $produk['id_kategori'] ?? null;
                    
                    if ($idKat) {
                        $produkByKategori[$idKat][] = $produk;
                    }
                }
            }

            // 4. Hitung total produk untuk setiap kategori
            // Kita gunakan pass by reference (&$kat) agar array asli berubah
            foreach ($kategori as &$kat) {
                $id = $kat['id'] ?? null;
                $kat['total_produk'] = ($id && isset($produkByKategori[$id])) ? count($produkByKategori[$id]) : 0;
            }
            unset($kat); // Hapus referensi terakhir

            $data = [
                'title'            => 'Manajemen Kategori',
                'kategori'         => $kategori,
                'produkByKategori' => $produkByKategori,
                'validation'       => Services::validation()
            ];

            return view('admin/kategori/index', $data);

        } catch (\Exception $e) {
            // Tampilkan error jika terjadi masalah koneksi database
            return "Terjadi Kesalahan Sistem: " . $e->getMessage();
        }
    }

    /**
     * Menyimpan kategori baru (AJAX Request)
     */
    public function store()
    {
        // 1. Validasi Input
        if (!$this->validate([
            'nama_kategori' => [
                'rules'  => 'required|min_length[3]|is_unique[kategori.nama_kategori]',
                'errors' => [
                    'required'   => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'is_unique'  => 'Nama kategori sudah ada.'
                ]
            ]
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            // 2. Simpan Data
            $this->kategoriModel->save([
                'nama_kategori' => $this->request->getPost('nama_kategori')
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori baru berhasil ditambahkan.'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['database' => 'Gagal menyimpan: ' . $e->getMessage()]
            ]);
        }
    }

    /**
     * Memperbarui kategori (AJAX Request)
     */
    public function update($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'ID kategori tidak valid.'
            ]);
        }

        // 1. Validasi Input (Exclude ID saat ini agar tidak terdeteksi duplikat dengan dirinya sendiri)
        if (!$this->validate([
            'nama_kategori' => [
                'rules'  => "required|min_length[3]|is_unique[kategori.nama_kategori,id,{$id}]",
                'errors' => [
                    'required'   => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'is_unique'  => 'Nama kategori sudah digunakan.'
                ]
            ]
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            // 2. Update Data
            $this->kategoriModel->update($id, [
                'nama_kategori' => $this->request->getPost('nama_kategori')
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori berhasil diperbarui.'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['database' => 'Gagal update: ' . $e->getMessage()]
            ]);
        }
    }

    /**
     * Menghapus kategori
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'ID Kategori tidak ditemukan.');
        }

        // Cek keberadaan kategori
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        /* CATATAN PENTING:
           Jika Anda ingin mencegah penghapusan kategori yang masih memiliki produk,
           buka komentar (uncomment) blok kode di bawah ini:
        */
        /*
        $produkCount = $this->produkModel->where('id_kategori', $id)->countAllResults();
        if ($produkCount > 0) {
             return redirect()->back()->with('error', 'Gagal hapus! Masih ada ' . $produkCount . ' produk dalam kategori ini.');
        }
        */

        try {
            // Proses Delete
            $this->kategoriModel->delete($id);
            return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->to('/admin/kategori')->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}