<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\CashRegisterModel; 
use App\Models\PenjualanModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $shiftModel;
    protected $penjualanModel;
    protected $userModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->shiftModel = new CashRegisterModel();
        $this->penjualanModel = new PenjualanModel();
        $this->userModel = new UserModel();
    }

    // 1. Dashboard Utama (Halaman Awal)
    public function index()
    {
        $userId = session()->get('user_id');
        
        // Cek Status Shift
        $activeShift = $this->shiftModel->getActiveShift($userId);
        
        // --- TAMBAHAN: AMBIL DATA STOK MENIPIS UNTUK DASHBOARD ---
        // Mengambil produk yang stoknya di bawah threshold (default 10)
        $lowStockProducts = $this->produkModel->getStokMenipis();

        // Hitung Kinerja Hari Ini (Hanya milik user ini)
        $todayStr = date('Y-m-d');
        $stats = $this->penjualanModel
            ->select('COUNT(id) as total_trx, SUM(total_harga) as total_omset')
            ->where('id_user', $userId)
            ->like('created_at', $todayStr, 'after') 
            ->first();

        $data = [
            'title'            => 'Dashboard Kasir',
            'shift'            => $activeShift,
            'todaySales'       => $stats['total_omset'] ?? 0,
            'todayTrx'         => $stats['total_trx'] ?? 0,
            'user'             => session()->get(),
            'lowStockProducts' => $lowStockProducts // Dikirim ke Views/kasir/dashboard.php
        ];

        return view('kasir/dashboard', $data);
    }

    // 2. Halaman POS (Mesin Kasir)
    public function pos()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->select('jam_selesai')->find($userId); 

        // Cek Status Shift
        $activeShift = $this->shiftModel->getActiveShift($userId);
        
        if ($activeShift) {
            // --- LOGIKA PENUTUPAN OTOMATIS & WARNING ---
            if (!empty($user['jam_selesai'])) {
                $endShiftTime = strtotime($user['jam_selesai']); 
                $forcedCloseTime = date('H:i:s', $endShiftTime + 900); // Toleransi 15 menit
                $currentTime = date('H:i:s');
                
                // 1. FORCED CLOSE
                if ($currentTime >= $forcedCloseTime) {
                    $shiftController = new \App\Controllers\Kasir\Shift();
                    $shiftController->autoCloseShift($activeShift); 

                    return redirect()->to('/logout')->with('error', 'Waktu shift Anda telah berakhir dan melebihi batas waktu toleransi. Shift ditutup otomatis.');
                } 
                
                // 2. WARNING
                $jamSelesaiStr = date('H:i:s', $endShiftTime);
                if ($currentTime >= $jamSelesaiStr) {
                    session()->setFlashdata('shift_warning', 'Shift Anda telah berakhir. Harap segera tutup kasir untuk mencegah penutupan otomatis.');
                }
            }
        }
        
        if (!$activeShift) {
            return redirect()->to('/kasir')->with('error', 'Silakan Buka Kasir (Shift) terlebih dahulu sebelum berjualan!');
        }

        // Ambil produk dan tambahkan status is_low (Stok Menipis)
        $produkList = $this->produkModel
                        ->select('produk.*, kategori.nama_kategori')
                        ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                        ->where('produk.stok >', 0)
                        ->orderBy('produk.nama_produk', 'ASC')
                        ->findAll();

        foreach ($produkList as &$p) {
            // Gunakan helper dari ProdukModel untuk konsistensi label
            $p['is_low'] = $this->produkModel->isLowStock($p['stok']);
        }

        $data = [
            'title'      => 'Mesin Kasir',
            'kategori'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'produk'     => $produkList,
        ];

        return view('kasir/index', $data);
    }

    // 3. API Endpoint untuk mengambil semua produk (Digunakan oleh Alpine.js/AJAX)
    public function getProductsApi()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $produk = $this->produkModel
                       ->select('produk.*, kategori.nama_kategori')
                       ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                       ->where('produk.stok >', 0)
                       ->orderBy('produk.nama_produk', 'ASC')
                       ->findAll();
        
        // Suntikkan status is_low ke setiap item sebelum dikirim ke Frontend
        foreach ($produk as &$p) {
            $p['is_low'] = $this->produkModel->isLowStock($p['stok']);
        }
        
        $data = [
            'products'  => $produk,
            'csrf_hash' => csrf_hash()
        ];

        return $this->response->setJSON($data);
    }
}