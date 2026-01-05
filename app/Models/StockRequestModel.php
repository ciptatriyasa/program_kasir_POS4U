<?php

namespace App\Models;

use CodeIgniter\Model;

class StockRequestModel extends Model
{
    protected $table            = 'stock_requests';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id_user_kasir', 
        'id_supplier',
        'id_produk', 
        'jumlah_diminta', 
        'alasan', 
        'status', 
        'id_user_admin'
    ];
    protected $useTimestamps    = true;
}