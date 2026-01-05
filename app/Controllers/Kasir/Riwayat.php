<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;

class Riwayat extends BaseController
{
    protected $penjualanModel;
    protected $detailPenjualanModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
    }

    // Menampilkan halaman utama riwayat transaksi kasir
    public function index()
    {
        $userId = session()->get('user_id');

        $penjualan = $this->penjualanModel
                         ->where('id_user', $userId)
                         ->orderBy('tanggal_penjualan', 'DESC')
                         ->findAll();
        
        $data = [
            'title'     => 'Riwayat Transaksi Saya',
            'penjualan' => $penjualan,
        ];

        return view('kasir/riwayat/index', $data);
    }

    // API Endpoint untuk mengambil detail transaksi (dipanggil dari Modal)
    public function getDetailApi($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        
        $userId = session()->get('user_id');

        $penjualan = $this->penjualanModel
                         ->select('penjualan.*, users.nama_lengkap as kasir_nama')
                         ->join('users', 'users.id = penjualan.id_user', 'left')
                         ->where('penjualan.id', $id)
                         ->where('penjualan.id_user', $userId) // Filter hanya milik kasir ini
                         ->first();

        if (!$penjualan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaksi tidak ditemukan atau bukan milik Anda.']);
        }

        $detail = $this->detailPenjualanModel
                      ->select('detail_penjualan.*, produk.nama_produk')
                      ->join('produk', 'produk.id = detail_penjualan.id_produk', 'left')
                      ->where('id_penjualan', $id)
                      ->findAll();
        
        // Mengubah format harga agar mudah ditampilkan di JS/AlpineJS
        $penjualan['total_harga_formatted'] = number_format($penjualan['total_harga'], 0, ',', '.');
        $penjualan['uang_dibayar_formatted'] = number_format($penjualan['uang_dibayar'], 0, ',', '.');
        $penjualan['kembalian_formatted'] = number_format($penjualan['kembalian'], 0, ',', '.');

        return $this->response->setJSON([
            'status' => 'success',
            'penjualan' => $penjualan,
            'detail' => $detail
        ]);
    }
}