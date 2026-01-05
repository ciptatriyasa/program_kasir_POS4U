<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StockRequestModel;
use App\Models\ProdukModel;
use App\Models\SupplierCatalogModel;

class StockRequestController extends BaseController
{
    protected $stockRequestModel;
    protected $produkModel;
    protected $catalogModel;

    public function __construct()
    {
        $this->stockRequestModel = new StockRequestModel();
        $this->produkModel = new ProdukModel();
        $this->catalogModel = new SupplierCatalogModel(); // Load Catalog Model
        helper(['form']);
    }

    public function index()
    {
        $requests = $this->stockRequestModel
             ->select('stock_requests.*, users.nama_lengkap as kasir_nama, produk.nama_produk, suppliers.nama_supplier')
             ->join('users', 'users.id = stock_requests.id_user_kasir', 'left')
             ->join('produk', 'produk.id = stock_requests.id_produk', 'left')
             ->join('suppliers', 'suppliers.id = stock_requests.id_supplier', 'left')
             ->orderBy('created_at', 'DESC')
             ->findAll();

        return view('admin/stock_requests/index', ['title' => 'Verifikasi Stok', 'requests' => $requests]);
    }

    public function process($id)
    {
        $request = $this->stockRequestModel->find($id);
        if (!$request) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $action = $this->request->getPost('action');

        if ($action === 'approve') {
            // 1. Get Catalog Item info
            $catalogItem = $this->catalogModel
                                ->where('id_supplier', $request['id_supplier'])
                                ->where('id_produk', $request['id_produk'])
                                ->first();

            if (!$catalogItem) {
                return redirect()->back()->with('error', 'Item tidak ditemukan di katalog supplier ini.');
            }

            // 2. Check Supplier Stock
            if ($catalogItem['stok_tersedia'] < $request['jumlah_diminta']) {
                return redirect()->back()->with('error', 'Stok supplier tidak mencukupi (Tersedia: '.$catalogItem['stok_tersedia'].').');
            }

            // 3. Deduct Supplier Stock
            $this->catalogModel->update($catalogItem['id'], [
                'stok_tersedia' => $catalogItem['stok_tersedia'] - $request['jumlah_diminta']
            ]);

            // 4. Update Main Product (Stock, Price, AND Exp Date)
            $currentProduct = $this->produkModel->find($request['id_produk']);
            
            $this->produkModel->update($request['id_produk'], [
                'stok'       => $currentProduct['stok'] + $request['jumlah_diminta'],
                'harga_beli' => $catalogItem['harga_dari_supplier'],
                'exp_date'   => $catalogItem['exp_date'] // <-- Update Exp Date sesuai katalog
            ]);

            // 5. Update Request Status
            $this->stockRequestModel->update($id, [
                'status'        => 'approved',
                'id_user_admin' => session()->get('user_id'),
            ]);

            return redirect()->back()->with('success', 'Disetujui. Stok, Harga, dan Exp Date diperbarui.');

        } elseif ($action === 'reject') {
            $this->stockRequestModel->update($id, [
                'status'        => 'rejected',
                'id_user_admin' => session()->get('user_id'),
            ]);
            return redirect()->back()->with('success', 'Permintaan ditolak.');
        }
    }
}