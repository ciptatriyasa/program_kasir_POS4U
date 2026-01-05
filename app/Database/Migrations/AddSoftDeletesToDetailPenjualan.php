<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeletesToDetailPenjualan extends Migration
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
        $this->forge->addColumn('detail_penjualan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_penjualan', 'deleted_at');
    }
}