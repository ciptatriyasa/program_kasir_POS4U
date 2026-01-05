<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCashRegistersTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'modal_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'total_penjualan_sistem' => [ // Total uang cash yang SEHARUSNYA ada dari penjualan
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'total_uang_fisik' => [ // Uang yang dihitung manual oleh kasir saat tutup
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['open', 'closed'],
                'default'    => 'open',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'opened_at' => ['type' => 'DATETIME', 'null' => true],
            'closed_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cash_registers');
    }

    public function down()
    {
        $this->forge->dropTable('cash_registers');
    }
}