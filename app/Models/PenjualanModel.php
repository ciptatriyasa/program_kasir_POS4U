<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table            = 'penjualan';
    protected $primaryKey       = 'id';
    
    // --- TAMBAHAN UNTUK SOFT DELETE ---
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    // --- AKHIR TAMBAHAN ---
    
    // Pastikan field baru/yang diperbarui sudah masuk
    protected $allowedFields    = ['id_user', 'total_harga', 'tanggal_penjualan', 'metode_pembayaran', 'uang_dibayar', 'kembalian', 'deleted_at'];
    protected $useTimestamps    = true;
}