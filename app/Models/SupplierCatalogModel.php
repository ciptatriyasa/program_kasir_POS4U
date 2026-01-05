<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierCatalogModel extends Model
{
    protected $table            = 'supplier_catalogs';
    protected $primaryKey       = 'id';
    
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    
    protected $allowedFields    = ['id_supplier', 'id_produk', 'nama_item', 'harga_dari_supplier', 'stok_tersedia', 'exp_date', 'deleted_at'];
    protected $useTimestamps    = true;
}