<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeletesToSupplierCatalogs extends Migration
{
    public function up()
    {
        $fields = [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at', // Menempatkan setelah kolom updated_at
            ],
        ];
        // Menambahkan kolom deleted_at ke tabel supplier_catalogs
        $this->forge->addColumn('supplier_catalogs', $fields);
    }

    public function down()
    {
        // Menghapus kolom deleted_at
        $this->forge->dropColumn('supplier_catalogs', 'deleted_at');
    }
}