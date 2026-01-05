<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSupplierCatalogs extends Migration
{
    public function up()
    {
        // 1. Ubah id_produk agar boleh NULL (karena saat supplier input, produk belum ada di admin)
        $this->forge->modifyColumn('supplier_catalogs', [
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, 
            ],
        ]);

        // 2. Tambahkan kolom nama_item (untuk nama sementara dari supplier)
        $fields = [
            'nama_item' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'id_produk'
            ]
        ];
        $this->forge->addColumn('supplier_catalogs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('supplier_catalogs', 'nama_item');
        // Mengembalikan ke NOT NULL agak risiko jika ada data null, jadi biarkan null
    }
}