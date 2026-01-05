<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPenjualanModel extends Model
{
    protected $table            = 'detail_penjualan';
    protected $primaryKey       = 'id';
    
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    protected $allowedFields    = ['id_penjualan', 'id_produk', 'jumlah', 'subtotal', 'deleted_at', 'harga_beli_snapshot'];
    protected $useTimestamps    = true;

    public function getProdukTerlaris($bulan, $tahun, $limit = 5)
    {
        return $this->select('produk.nama_produk, SUM(detail_penjualan.jumlah) as total_terjual')
                    ->join('produk', 'produk.id = detail_penjualan.produk_id')
                    ->join('penjualan', 'penjualan.id = detail_penjualan.penjualan_id')
                    ->where('MONTH(penjualan.created_at)', $bulan) // Pastikan kolom tanggal di DB adalah created_at atau sesuaikan
                    ->where('YEAR(penjualan.created_at)', $tahun)
                    ->groupBy('detail_penjualan.produk_id')
                    ->orderBy('total_terjual', 'DESC')
                    ->findAll($limit);
    }
}