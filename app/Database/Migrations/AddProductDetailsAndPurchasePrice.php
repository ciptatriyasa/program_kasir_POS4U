<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductDetailsAndPurchasePrice extends Migration
{
    public function up()
    {
        $fields = [
            'foto_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'barcode',
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'after'      => 'harga',
                'default'    => 0.00,
            ],
            'exp_date' => [
                'type'       => 'DATE',
                'null'       => true,
                'after'      => 'stok',
            ],
            'posisi_rak' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'exp_date',
            ],
        ];
        $this->forge->addColumn('produk', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produk', ['foto_produk', 'harga_beli', 'exp_date', 'posisi_rak']);
    }
}