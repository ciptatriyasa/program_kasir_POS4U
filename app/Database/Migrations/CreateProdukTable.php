<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdukTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true, // <-- TAMBAHKAN INI
            ],
            // ... (sisa field tidak berubah) ...
        ]);
        $this->forge->addKey('id', true);

        // --- UBAH BARIS DI BAWAH INI ---
        // LAMA: $this->forge->addForeignKey('id_kategori', 'kategori', 'id', 'CASCADE', 'CASCADE');
        // BARU:
        $this->forge->addForeignKey('id_kategori', 'kategori', 'id', 'CASCADE', 'SET NULL');
        // --- AKHIR PERUBAHAN ---
        
        $this->forge->createTable('produk');
    }

    public function down()
    {
        $this->forge->dropTable('produk');
    }
}