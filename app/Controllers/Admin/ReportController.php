<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\UserModel; // Import User Model

class ReportController extends BaseController
{
    protected $penjualanModel;
    protected $detailPenjualanModel;
    protected $userModel; // Property User Model

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->userModel = new UserModel(); // Inisialisasi User Model
    }

    public function index()
    {
        return $this->generateReport();
    }

    public function generate()
    {
        return $this->generateReport();
    }

    // Redirect helper agar jika diakses via URL langsung tetap masuk ke format laporan utama
    public function cashierPerformance()
    {
        return redirect()->to('admin/reports?report_type=cashier_performance');
    }

    private function generateReport()
    {
        // 1. Ambil input Report Type
        $reportType = $this->request->getVar('report_type');

        // 2. Ambil input Tanggal
        $startDate = $this->request->getVar('start_date');
        $endDate   = $this->request->getVar('end_date');

        // 3. Logika Default Tanggal (Awal bulan s/d Hari ini)
        if (empty($startDate)) {
            $startDate = date('Y-m-01'); 
        }
        if (empty($endDate)) {
            $endDate = date('Y-m-d');
        }

        // Setup variabel dasar untuk View
        $data = [
            'title'      => 'Laporan',
            'reportType' => $reportType,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'results'    => [],
            'total'      => 0,
            'summary'    => [ 
                'total_omset'  => 0,
                'total_hpp'    => 0,
                'total_profit' => 0
            ] 
        ];

        // Jika user belum memilih jenis laporan, return view index awal
        if (!$reportType) {
            return view('admin/reports/index', $data);
        }

        // Format jam lengkap untuk query database
        $queryStart = $startDate . ' 00:00:00';
        $queryEnd   = $endDate . ' 23:59:59';

        // --- LOGIKA PEMILIHAN LAPORAN ---

        if ($reportType === 'sales') {
            // 1. Laporan Penjualan
            $data['results'] = $this->getSalesReport($queryStart, $queryEnd);
            $data['total']   = array_sum(array_column($data['results'], 'total_harga'));
            
        } elseif ($reportType === 'products') {
            // 2. Laporan Produk Terlaris
            $data['results'] = $this->getProductReport($queryStart, $queryEnd);
            $data['total']   = array_sum(array_column($data['results'], 'total_jumlah'));
            
        } elseif ($reportType === 'profit_loss') {
            // 3. Laporan Laba Rugi
            $results = $this->getProfitLossReport($queryStart, $queryEnd);
            $data['results'] = $results;

            foreach ($results as $row) {
                $data['summary']['total_omset']  += $row['omset'];
                $data['summary']['total_hpp']    += $row['total_hpp'];
                $data['summary']['total_profit'] += $row['laba_kotor'];
            }

        } elseif ($reportType === 'cashier_performance') {
            // 4. [BARU] Laporan Kinerja Kasir
            $data['results'] = $this->getCashierPerformanceData($queryStart, $queryEnd);
            // Total omset dari semua kasir
            $data['total'] = array_sum(array_column($data['results'], 'total_omset'));
        }

        return view('admin/reports/index', $data);
    }

    // --- QUERY METHODS ---

    // [BARU] Method Khusus Kinerja Kasir
    private function getCashierPerformanceData($start, $end)
    {
        return $this->penjualanModel
            ->select('penjualan.id_user, users.nama_lengkap, users.username, COUNT(penjualan.id) as total_trx, SUM(penjualan.total_harga) as total_omset')
            ->join('users', 'users.id = penjualan.id_user', 'left')
            // Filter: Hanya ambil user yang role-nya 'kasir'
            ->where('users.role', 'kasir') 
            ->where('penjualan.tanggal_penjualan >=', $start)
            ->where('penjualan.tanggal_penjualan <=', $end)
            ->groupBy('penjualan.id_user')
            ->orderBy('total_omset', 'DESC')
            ->withDeleted()
            ->findAll();
    }

    private function getSalesReport($start, $end)
    {
        return $this->penjualanModel
            ->withDeleted()
            ->select('penjualan.*, users.nama_lengkap as kasir_nama')
            ->join('users', 'users.id = penjualan.id_user', 'left')
            ->where('tanggal_penjualan >=', $start)
            ->where('tanggal_penjualan <=', $end)
            ->orderBy('tanggal_penjualan', 'DESC')
            ->findAll();
    }

    private function getProductReport($start, $end)
    {
        return $this->detailPenjualanModel
            ->withDeleted()
            ->select('id_produk, produk.nama_produk, SUM(jumlah) as total_jumlah, SUM(subtotal) as total_subtotal')
            ->join('produk', 'produk.id = detail_penjualan.id_produk', 'left')
            ->join('penjualan', 'penjualan.id = detail_penjualan.id_penjualan')
            ->where('penjualan.tanggal_penjualan >=', $start)
            ->where('penjualan.tanggal_penjualan <=', $end)
            ->groupBy('id_produk, produk.nama_produk')
            ->orderBy('total_jumlah', 'DESC')
            ->findAll();
    }

    private function getProfitLossReport($start, $end)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT 
                    DATE(p.tanggal_penjualan) as tanggal,
                    COUNT(DISTINCT p.id) as total_transaksi,
                    SUM(d.subtotal) as omset,
                    SUM(d.jumlah * d.harga_beli_snapshot) as total_hpp,
                    (SUM(d.subtotal) - SUM(d.jumlah * d.harga_beli_snapshot)) as laba_kotor
                FROM detail_penjualan d
                JOIN penjualan p ON p.id = d.id_penjualan
                WHERE p.tanggal_penjualan BETWEEN ? AND ?
                AND p.deleted_at IS NULL 
                GROUP BY DATE(p.tanggal_penjualan)
                ORDER BY tanggal DESC";

        return $db->query($sql, [$start, $end])->getResultArray();
    }
}