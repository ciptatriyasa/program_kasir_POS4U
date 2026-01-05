<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SupplierCatalogModel;
use App\Models\StockRequestModel;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $kategoriModel;
    protected $produkModel;
    protected $catalogModel;
    protected $stockRequestModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->kategoriModel = new KategoriModel();
        $this->produkModel = new ProdukModel();
        $this->catalogModel = new SupplierCatalogModel();
        $this->stockRequestModel = new StockRequestModel();
        helper('form');
    }

    // 1. List Suppliers & History dengan Filter Hari dan Bulan
    public function index()
    {
        // A. Ambil Data Supplier
        $suppliers = $this->supplierModel
            ->select('suppliers.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id = suppliers.id_kategori', 'left')
            ->orderBy('nama_supplier', 'ASC')
            ->findAll();

        // B. Logika Filter Riwayat
        $filterSupplierId = $this->request->getGet('filter_supplier');
        $filterDate       = $this->request->getGet('filter_date');   // Filter Hari/Tanggal
        $filterMonth      = $this->request->getGet('filter_month');  // Filter Bulan
        $today            = date('Y-m-d');

        // Builder untuk Riwayat (Barang Masuk/Approved)
        $historyBuilder = $this->stockRequestModel
            ->select('stock_requests.*, suppliers.nama_supplier, kategori.nama_kategori, produk.nama_produk')
            ->join('suppliers', 'suppliers.id = stock_requests.id_supplier', 'left')
            ->join('kategori', 'kategori.id = suppliers.id_kategori', 'left') 
            ->join('produk', 'produk.id = stock_requests.id_produk', 'left')
            ->where('stock_requests.status', 'approved');

        // Logika Prioritas Filter Waktu
        if (!empty($filterDate)) {
            // Jika filter hari (tanggal) dipilih
            $historyBuilder->where('DATE(stock_requests.updated_at)', $filterDate);
        } elseif (!empty($filterMonth)) {
            // Jika filter bulan dipilih (format input month biasanya YYYY-MM)
            $historyBuilder->where("DATE_FORMAT(stock_requests.updated_at, '%Y-%m')", $filterMonth);
        } else {
            // Default: Tampilkan hari ini jika tidak ada filter waktu yang dipilih
            $historyBuilder->where('DATE(stock_requests.updated_at)', $today);
        }

        // Terapkan Filter Supplier jika dipilih
        if (!empty($filterSupplierId)) {
            $historyBuilder->where('stock_requests.id_supplier', $filterSupplierId);
        }

        $historyData = $historyBuilder->orderBy('stock_requests.updated_at', 'DESC')->findAll();

        $data = [
            'title'          => 'Manajemen Supplier',
            'suppliers'      => $suppliers,
            'kategori'       => $this->kategoriModel->findAll(),
            'history'        => $historyData,
            'filterSupplier' => $filterSupplierId,
            'filterDate'     => $filterDate,
            'filterMonth'    => $filterMonth,
            'today'          => $today
        ];

        return view('admin/suppliers/index', $data);
    }

    // Menyimpan Supplier Baru (AJAX Request)
    public function create()
    {
        if (!$this->validate([
            'nama_supplier' => 'required|min_length[3]',
            'id_kategori'   => 'required|integer',
            'email'         => 'permit_empty|valid_email'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $this->supplierModel->save([
            'nama_supplier' => $this->request->getVar('nama_supplier'),
            'id_kategori'   => $this->request->getVar('id_kategori'),
            'alamat'        => $this->request->getVar('alamat'),
            'no_telp'       => $this->request->getVar('no_telp'),
            'email'         => $this->request->getVar('email'),
        ]);
        
        if (function_exists('log_activity')) {
             log_activity('create_supplier', 'Admin menambah supplier baru: ' . $this->request->getVar('nama_supplier'));
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Supplier baru berhasil ditambahkan.'
        ]);
    }

    // Memperbarui Supplier (AJAX Request)
    public function update($id = null)
    {
        if (!$this->validate([
            'nama_supplier' => 'required|min_length[3]',
            'id_kategori'   => 'required|integer',
            'email'         => 'permit_empty|valid_email'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $this->supplierModel->update($id, [
            'nama_supplier' => $this->request->getVar('nama_supplier'),
            'id_kategori'   => $this->request->getVar('id_kategori'),
            'alamat'        => $this->request->getVar('alamat'),
            'no_telp'       => $this->request->getVar('no_telp'),
            'email'         => $this->request->getVar('email'),
        ]);
        
        if (function_exists('log_activity')) {
             log_activity('update_supplier', 'Admin memperbarui supplier: ' . $this->request->getVar('nama_supplier') . ' (ID: ' . $id . ')');
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data supplier berhasil diperbarui.'
        ]);
    }

    public function delete($id)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) return redirect()->to('/admin/suppliers')->with('error', 'Supplier tidak ditemukan.');

        $this->supplierModel->delete($id);
        $this->catalogModel
             ->where('id_supplier', $id)
             ->set('deleted_at', date('Y-m-d H:i:s'))
             ->update();
        
        if (function_exists('log_activity')) {
            log_activity('soft_delete_supplier', 'Admin soft delete supplier: ' . $supplier['nama_supplier'] . ' (ID: ' . $id . ')');
        }

        return redirect()->to('/admin/suppliers')->with('success', 'Supplier berhasil dihapus.');
    }

    public function show($id)
    {
        $supplier = $this->supplierModel
                         ->select('suppliers.*, kategori.nama_kategori')
                         ->join('kategori', 'kategori.id = suppliers.id_kategori', 'left')
                         ->find($id);

        if (!$supplier) return redirect()->to('/admin/suppliers')->with('error', 'Supplier tidak ditemukan.');

        $data = ['title' => 'Detail Supplier', 'supplier' => $supplier];
        return view('admin/suppliers/show', $data);
    }

    public function catalog($idSupplier)
    {
        $supplier = $this->supplierModel
                        ->select('suppliers.*, kategori.nama_kategori')
                        ->join('kategori', 'kategori.id = suppliers.id_kategori', 'left')
                        ->find($idSupplier);

        if (!$supplier) return redirect()->to('/admin/suppliers')->with('error', 'Supplier tidak ditemukan.');

        $catalog = $this->catalogModel
                        ->select('supplier_catalogs.*, produk.nama_produk, produk.barcode')
                        ->join('produk', 'produk.id = supplier_catalogs.id_produk', 'left') 
                        ->where('id_supplier', $idSupplier)
                        ->orderBy('supplier_catalogs.created_at', 'DESC')
                        ->findAll();

        $data = [
            'title'    => 'Kelola Katalog: ' . $supplier['nama_supplier'],
            'supplier' => $supplier,
            'catalog'  => $catalog,
        ];

        return view('admin/suppliers/catalog', $data);
    }

    public function addCatalogItem($idSupplier)
    {
        if (!$this->validate([
            'nama_item'           => 'required|min_length[3]',
            'harga_dari_supplier' => 'required|numeric|greater_than[0]',
            'stok_tersedia'       => 'required|integer|greater_than_equal_to[0]',
            'exp_date'            => 'permit_empty|valid_date[Y-m-d]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->catalogModel->save([
            'id_supplier'         => $idSupplier,
            'nama_item'           => $this->request->getVar('nama_item'),
            'harga_dari_supplier' => $this->request->getVar('harga_dari_supplier'),
            'stok_tersedia'       => $this->request->getVar('stok_tersedia'),
            'exp_date'            => $this->request->getVar('exp_date') ?: null,
        ]);
        
        return redirect()->back()->with('success', 'Item berhasil ditambahkan.');
    }

    public function updateCatalogItem($id)
    {
        if (!$this->validate([
            'harga_dari_supplier' => 'required|numeric|greater_than[0]',
            'stok_tersedia'       => 'required|integer|greater_than_equal_to[0]',
            'exp_date'            => 'permit_empty|valid_date[Y-m-d]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Gagal update.');
        }

        $this->catalogModel->update($id, [
            'harga_dari_supplier' => $this->request->getVar('harga_dari_supplier'),
            'stok_tersedia'       => $this->request->getVar('stok_tersedia'),
            'exp_date'            => $this->request->getVar('exp_date') ?: null,
        ]);

        return redirect()->back()->with('success', 'Katalog diperbarui.');
    }

    public function deleteCatalogItem($id)
    {
        $this->catalogModel->delete($id);
        return redirect()->back()->with('success', 'Item dihapus.');
    }
}