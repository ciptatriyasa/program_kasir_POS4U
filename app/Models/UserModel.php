<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';

    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    
    // TAMBAHKAN FIELD BARU DI SINI
    protected $allowedFields    = [
        'nama_lengkap', 
        'username', 
        'password', 
        'role', 
        'jam_mulai', 
        'jam_selesai',
        'email',
        'no_hp',
        'alamat',
        'foto_profil',
        'deleted_at',
        'jam_kerja_start',
        'jam_kerja_end'
    ];

    // Aktifkan auto-timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Hash password otomatis sebelum insert atau update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword']; // Tambahkan ini untuk update

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}