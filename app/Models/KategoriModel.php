<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{

    protected $table            = 'kategori';
    protected $primaryKey       = 'id';
    
    // Field yang boleh diisi
    protected $allowedFields    = ['nama_kategori', 'deleted_at']; 

    // Konfigurasi Soft Deletes
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    // Konfigurasi Timestamps (Otomatis mengisi created_at & updated_at)
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}