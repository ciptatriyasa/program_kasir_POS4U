<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\StockRequestModel;
use App\Models\SupplierModel;
use App\Models\SupplierCatalogModel;

class Stock extends BaseController
{
    protected $produkModel;
    protected $stockRequestModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->stockRequestModel = new StockRequestModel();
        $this->supplierModel = new SupplierModel();
        helper(['form']);
    }

    public function index()
    {
        $userId = session()->get('user_id');
        
        $allProduk = $this->produkModel
            ->select('produk.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->orderBy('produk.stok', 'ASC')
            ->findAll();

        // --- SINKRONISASI LOGIKA STOK MENIPIS ---
        foreach ($allProduk as &$p) {
            $p['is_low'] = $this->produkModel->isLowStock($p['stok']);
        }
        
        $riwayatRequests = $this->stockRequestModel
            ->select('stock_requests.*, produk.nama_produk, suppliers.nama_supplier')
            ->join('produk', 'produk.id = stock_requests.id_produk', 'left')
            ->join('suppliers', 'suppliers.id = stock_requests.id_supplier', 'left')
            ->where('id_user_kasir', $userId) 
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $suppliers = $this->supplierModel->select('id, nama_supplier')->orderBy('nama_supplier', 'ASC')->findAll();
        
        return view('kasir/stock/index', [
            'title' => 'Stok Barang',
            'produk' => $allProduk,
            'riwayat' => $riwayatRequests,
            'suppliers' => $suppliers, 
            'validation' => \Config\Services::validation()
        ]);
    }

    public function save()
    {
        $rules = [
            'id_supplier' => 'required|integer',
            'id_produk' => 'required|integer',
            'jumlah_diminta' => 'required|integer|greater_than[0]',
            'alasan' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->stockRequestModel->save([
            'id_user_kasir' => session()->get('user_id'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_produk' => $this->request->getPost('id_produk'),
            'jumlah_diminta' => $this->request->getPost('jumlah_diminta'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'pending',
        ]);

        return redirect()->to('/kasir/stock')->with('success', 'Permintaan stok terkirim. Menunggu persetujuan Admin.');
    }

    public function getSuppliersApi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        $suppliers = $this->supplierModel->select('id, nama_supplier')->orderBy('nama_supplier', 'ASC')->findAll();
        return $this->response->setJSON($suppliers);
    }

    public function getSupplierProductsApi($idSupplier)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $model = new SupplierCatalogModel();
        $products = $model
            ->select('supplier_catalogs.id_produk, produk.nama_produk, supplier_catalogs.stok_tersedia, supplier_catalogs.harga_dari_supplier')
            ->join('produk', 'produk.id = supplier_catalogs.id_produk')
            ->where('supplier_catalogs.id_supplier', $idSupplier)
            ->where('supplier_catalogs.stok_tersedia >', 0) 
            ->findAll();
            
        return $this->response->setJSON($products);
    }
}