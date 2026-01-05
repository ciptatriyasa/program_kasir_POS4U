<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\ProdukModel; // Tetap di-load, meskipun tidak digunakan di delete() ini

class PenjualanController extends BaseController
{
    protected $penjualanModel;
    protected $detailPenjualanModel;
    protected $produkModel; 

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->produkModel = new ProdukModel(); 
    }

    // Menampilkan daftar penjualan (Hanya yang aktif/belum di-soft delete)
    public function index()
    {
        // 1. Ambil semua data penjualan (Soft Delete Model otomatis mengecualikan yang deleted_at != NULL)
        $penjualanData = $this->penjualanModel
                            ->select('penjualan.*, users.nama_lengkap')
                            ->join('users', 'users.id = penjualan.id_user', 'left')
                            ->orderBy('tanggal_penjualan', 'DESC')
                            ->findAll(); //
        
        // 2. Hitung total transaksi untuk penomoran
        $totalTransaksi = count($penjualanData); //

        $data = [
            'penjualan'      => $penjualanData, //
            'totalTransaksi' => $totalTransaksi, //
            'title'          => 'Riwayat Penjualan' //
        ];
        return view('admin/penjualan/index', $data); //
    }

    // Menampilkan detail penjualan (Termasuk yang sudah di-soft delete/dibatalkan)
    public function show($id = null)
    {
        // Gunakan withDeleted() agar Admin tetap bisa melihat detail transaksi yang sudah dibatalkan
        $penjualan = $this->penjualanModel
                         ->withDeleted() //
                         ->select('penjualan.*, users.nama_lengkap') //
                         ->join('users', 'users.id = penjualan.id_user', 'left') //
                         ->find($id); //

        if (!$penjualan) { //
            return redirect()->to('/admin/penjualan')->with('error', 'Data penjualan tidak ditemukan.'); //
        }

        $detail = $this->detailPenjualanModel
                      ->withDeleted() //
                      ->select('detail_penjualan.*, produk.nama_produk') //
                      ->join('produk', 'produk.id = detail_penjualan.id_produk', 'left') //
                      ->where('id_penjualan', $id) //
                      ->findAll(); //

        $data = [
            'title'     => 'Detail Penjualan #' . $penjualan['id'], //
            'penjualan' => $penjualan, //
            'detail'    => $detail //
        ];
        return view('admin/penjualan/show', $data); //
    }

    /**
     * Pembatalan Penjualan (Soft Delete) TANPA Pengembalian Stok
     */
    public function delete($id = null)
    {
        $penjualan = $this->penjualanModel->find($id); //
        if (!$penjualan) { //
            return redirect()->to('/admin/penjualan')->with('error', 'Data penjualan tidak ditemukan.'); //
        }
        
        // Cek Metode Permintaan (Sesuai dengan Routes.php: POST)
        if (!$this->request->is('post')) { //
             return redirect()->back()->with('error', 'Metode permintaan tidak diizinkan.'); //
        }

        $db = \Config\Database::connect();
        $db->transStart(); // Mulai Transaksi //

        try {
            
            // 1. Lakukan Soft Delete pada Penjualan Utama
            $this->penjualanModel->delete($id); //

            // 2. Soft Delete SEMUA Detail Penjualan yang terhubung
            $this->detailPenjualanModel
                 ->where('id_penjualan', $id) //
                 ->set('deleted_at', date('Y-m-d H:i:s')) //
                 ->update(); //
            
            // 3. Rollback Stok ke Produk (Dihapus sesuai permintaan user)
            // Logika ini dihapus: tidak ada $detailsToRestore dan tidak ada loop update stok.
            
            $db->transComplete(); // Selesaikan Transaksi //

            // Log aktivitas (Diperbarui)
            if (function_exists('log_activity')) {
                log_activity('soft_delete_penjualan', 'Admin soft delete transaksi penjualan ID: ' . $id . ' (Total: Rp' . number_format($penjualan['total_harga'], 0, ',', '.') . '). Stok TIDAK diubah.');
            }

            // Pesan sukses (Diperbarui)
            return redirect()->to('/admin/penjualan')->with('success', 'Transaksi penjualan #' . $id . ' berhasil dihapus.');
            
        } catch (\Exception $e) { //
            $db->transRollback(); // Batalkan Transaksi jika terjadi error //
            log_message('error', 'Gagal soft delete penjualan ID ' . $id . ': ' . $e->getMessage()); //
            return redirect()->to('/admin/penjualan')->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage()); //
        }
    }
}   