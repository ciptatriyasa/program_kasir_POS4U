<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    // --- KUNCI KONSISTENSI STOK MENIPIS ---
    // Tentukan batas stok menipis di satu tempat agar Admin & Kasir selalu sama
    const LOW_STOCK_THRESHOLD = 10;

    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $table            = 'produk';
    protected $primaryKey       = 'id';
    
    protected $allowedFields    = [
        'id_kategori', 
        'nama_produk', 
        'barcode', 
        'foto_produk', 
        'harga', 
        'harga_beli', 
        'stok', 
        'exp_date', 
        'posisi_rak', 
        'deleted_at'
    ];

    protected $useTimestamps    = true;

    protected $validationRules = [
         'barcode' => 'permit_empty|alpha_numeric|is_unique[produk.barcode,id,{id}]',
         'harga_beli' => 'required|numeric|greater_than_equal_to[0]',
         'exp_date' => 'permit_empty|valid_date[Y-m-d]',
         'posisi_rak' => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
         'barcode' => [
             'is_unique' => 'Barcode ini sudah digunakan produk lain.',
             'alpha_numeric' => 'Barcode hanya boleh berisi huruf dan angka.'
         ],
         'harga_beli' => [
             'required' => 'Harga Beli wajib diisi.',
             'numeric' => 'Harga Beli harus berupa angka.',
             'greater_than_equal_to' => 'Harga Beli tidak boleh negatif.'
         ]
    ];
       
    /**
     * Helper untuk mengecek apakah stok masuk kategori menipis secara boolean.
     * Digunakan untuk logika tampilan di Kasir maupun Admin.
     */
    public function isLowStock($stok)
    {
        return ($stok > 0 && $stok <= self::LOW_STOCK_THRESHOLD);
    }

    public function addStock(int $produkId, int $jumlah)
    {
        return $this->set('stok', 'stok + ' . $jumlah, false)
                    ->where('id', $produkId)
                    ->update();
    }

    public function decrementStock(int $produkId, int $jumlah)
    {
        return $this->addStock($produkId, -$jumlah);
    }

    /**
     * Mengambil daftar produk yang stoknya menipis berdasarkan konstanta global.
     */
    public function getStokMenipis($batas = self::LOW_STOCK_THRESHOLD, $limit = 5)
    {
        return $this->where('stok <=', $batas)
                    ->where('stok >', 0)
                    ->orderBy('stok', 'ASC')
                    ->findAll($limit);
    }
}