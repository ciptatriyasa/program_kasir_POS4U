<?php

namespace App\Models;

use CodeIgniter\Model;

class CashRegisterModel extends Model
{
    protected $table            = 'cash_registers';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'modal_awal', 'total_penjualan_sistem', 
        'total_uang_fisik', 'status', 'catatan', 'opened_at', 'closed_at'
    ];
    
    // Helper untuk cek apakah user punya shift yang sedang buka
    public function getActiveShift($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'open')
                    ->first();
    }
}