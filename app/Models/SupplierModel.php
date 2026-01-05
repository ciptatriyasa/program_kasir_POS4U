<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_kategori', 'nama_supplier', 'alamat', 'no_telp', 'email', 'deleted_at'];
    protected $useTimestamps    = true;
}