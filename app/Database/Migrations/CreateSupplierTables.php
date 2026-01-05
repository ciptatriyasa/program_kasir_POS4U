<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplierTables extends Migration
{
    public function up()
    {
        // 1. Create Suppliers Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kategori' => [ // Suppliers specialize in specific categories
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true, 
            ],
            'nama_supplier' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true], // For SoftDeletes
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_kategori', 'kategori', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('suppliers');

        // 2. Create Supplier Catalogs Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'harga_dari_supplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'stok_tersedia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'exp_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_supplier', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_produk', 'produk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('supplier_catalogs');
    }

    public function down()
    {
        $this->forge->dropTable('supplier_catalogs');
        $this->forge->dropTable('suppliers');
    }
}