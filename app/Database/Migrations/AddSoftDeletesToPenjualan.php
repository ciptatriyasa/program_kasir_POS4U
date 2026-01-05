<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeletesToPenjualan extends Migration
{
    public function up()
    {
        $fields = [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
        ];
        // HANYA menambahkan kolom deleted_at
        $this->forge->addColumn('penjualan', $fields); 
    }

    public function down()
    {
        // HANYA menghapus kolom deleted_at
        $this->forge->dropColumn('penjualan', 'deleted_at');
    }
}