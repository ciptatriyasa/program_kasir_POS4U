<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\StockRequestModel;
use App\Models\KategoriModel;
use App\Models\CashRegisterModel;
use App\Models\ProdukModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // 1. Instansiasi Model
        $penjualanModel = new PenjualanModel();
        $detailModel    = new DetailPenjualanModel();
        $userModel      = new UserModel();
        $stockModel     = new StockRequestModel();
        $kategoriModel  = new KategoriModel();
        $shiftModel     = new CashRegisterModel();
        $produkModel    = new ProdukModel();

        // 2. Setup Tanggal
        $currentYear  = date('Y');
        $currentMonth = date('m');
        $todayDate    = date('Y-m-d');
        
        // =====================================================================
        // BAGIAN 1: STATISTIK UTAMA (TAHUNAN & HARIAN)
        // =====================================================================
        
        // Total Item Terjual (Tahun Ini)
        $totalItem = $detailModel->selectSum('jumlah')
            ->join('penjualan', 'penjualan.id = detail_penjualan.id_penjualan') 
            ->where('YEAR(penjualan.tanggal_penjualan)', $currentYear)
            ->get()->getRow()->jumlah ?? 0;
        
        // Total Income (Tahun Ini)
        $totalIncome = $penjualanModel->selectSum('total_harga')
            ->where('YEAR(tanggal_penjualan)', $currentYear)
            ->get()->getRow()->total_harga ?? 0;

        // Total Transaksi (Tahun Ini)
        $totalTransaksi = $penjualanModel
            ->where('YEAR(tanggal_penjualan)', $currentYear)
            ->countAllResults();
        
        // Penjualan Hari Ini (Daily Sales)
        $todaySales = $penjualanModel->selectSum('total_harga')
            ->where('DATE(tanggal_penjualan)', $todayDate)
            ->get()->getRow()->total_harga ?? 0;

        // Transaksi Hari Ini
        $todayTransactions = $penjualanModel
            ->where('DATE(tanggal_penjualan)', $todayDate)
            ->countAllResults();

        // =====================================================================
        // BAGIAN 2: STATISTIK PENDUKUNG
        // =====================================================================
        $totalUser  = $userModel->countAllResults();
        $pendingReq = $stockModel->where('status', 'pending')->countAllResults();
        $kasirAktif = $shiftModel->where('status', 'open')->countAllResults(); 

        // =====================================================================
        // BAGIAN 3: FITUR TABLE (STOK & TERLARIS)
        // =====================================================================
        
        // Stok Menipis (Limit 5 item dengan stok <= 10)
        $stokMenipis = $produkModel->where('stok <=', 10)
            ->where('stok >', 0)
            ->orderBy('stok', 'ASC')
            ->findAll(5); 

        // Produk Terlaris Bulan Ini (Top 5)
        $produkTerlaris = $detailModel->select('produk.nama_produk, SUM(detail_penjualan.jumlah) as total_terjual')
            ->join('produk', 'produk.id = detail_penjualan.id_produk') 
            ->join('penjualan', 'penjualan.id = detail_penjualan.id_penjualan')
            ->where('MONTH(penjualan.tanggal_penjualan)', $currentMonth)
            ->where('YEAR(penjualan.tanggal_penjualan)', $currentYear)
            ->groupBy('detail_penjualan.id_produk')
            ->orderBy('total_terjual', 'DESC')
            ->findAll(5);

        // =====================================================================
        // BAGIAN 4: DATA CHART / GRAFIK
        // =====================================================================

        // A. Chart Bulanan (Line Chart)
        $monthlySales = $penjualanModel
            ->select("MONTH(tanggal_penjualan) as month, SUM(total_harga) as total")
            ->where('YEAR(tanggal_penjualan)', $currentYear)
            ->groupBy('MONTH(tanggal_penjualan)')
            ->orderBy('month', 'ASC')
            ->findAll();
        
        // Siapkan array kosong untuk 12 bulan
        $monthlyData = array_fill(1, 12, 0);
        $monthlyLabels = [];

        // Isi data bulan yang ada penjualannya
        foreach ($monthlySales as $sale) {
            $monthlyData[(int)$sale['month']] = (float)$sale['total'];
        }
        
        // Generate label bulan (Jan, Feb, ...)
        for ($m=1; $m<=12; $m++) {
            $monthlyLabels[] = date('M', mktime(0, 0, 0, $m, 1, $currentYear));
        }
        
        // B. Chart Harian (Last 7 Days)
        $dailySalesLabels = [];
        $dailySalesData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            // PERBAIKAN: Menggunakan getRow() agar lebih aman dari null
            $sales = $penjualanModel
                ->selectSum('total_harga')
                ->where('DATE(tanggal_penjualan)', $date)
                ->get()->getRow()->total_harga ?? 0;
            
            $dailySalesLabels[] = date('D', strtotime($date)); // Mon, Tue, etc
            $dailySalesData[] = (float) $sales;
        }

        // C. Chart Komparasi Tahunan (Current vs Last Year)
        $lastYear = $currentYear - 1;
        
        $annualSalesCurrent = $penjualanModel
            ->select("MONTH(tanggal_penjualan) as month, SUM(total_harga) as total")
            ->where('YEAR(tanggal_penjualan)', $currentYear)
            ->groupBy('MONTH(tanggal_penjualan)')
            ->orderBy('month', 'ASC')
            ->findAll();

        $annualSalesLast = $penjualanModel
            ->select("MONTH(tanggal_penjualan) as month, SUM(total_harga) as total")
            ->where('YEAR(tanggal_penjualan)', $lastYear)
            ->groupBy('MONTH(tanggal_penjualan)')
            ->orderBy('month', 'ASC')
            ->findAll();

        // Format data agar index 1-12 terisi (default 0)
        $annualDataCurrent = array_fill(1, 12, 0);
        foreach ($annualSalesCurrent as $sale) {
            $annualDataCurrent[(int)$sale['month']] = (float)$sale['total'];
        }
        
        $annualDataLast = array_fill(1, 12, 0);
        foreach ($annualSalesLast as $sale) {
            $annualDataLast[(int)$sale['month']] = (float)$sale['total'];
        }

        // =====================================================================
        // BAGIAN 5: KIRIM DATA KE VIEW
        // =====================================================================
        $data = [
            'title' => 'Dashboard Admin',
            
            // Cards Data
            'totalItemTerjual' => $totalItem,
            'totalIncome'      => $totalIncome,
            'totalTransaksi'   => $totalTransaksi,
            'todaySales'       => $todaySales,
            'todayTransactions'=> $todayTransactions,
            
            // Tables Data
            'stokMenipis'      => $stokMenipis,
            'produkTerlaris'   => $produkTerlaris,

            // Widgets Data
            'totalUserAktif'       => $totalUser,
            'pendingStockRequests' => $pendingReq,
            'kasirAktif'           => $kasirAktif, 
            'kategori'             => $kategoriModel->findAll(),
            
            // Charts Data (Penting!)
            'monthlySalesLabels' => $monthlyLabels,
            'monthlySalesData'   => array_values($monthlyData),
            'dailySalesLabels'   => $dailySalesLabels,
            'dailySalesData'     => $dailySalesData,
            'annualDataCurrent'  => array_values($annualDataCurrent),
            'annualDataLast'     => array_values($annualDataLast),
            'currentYear'        => $currentYear,
            'lastYear'           => $lastYear,
        ];

        return view('admin/index', $data); 
    }
}