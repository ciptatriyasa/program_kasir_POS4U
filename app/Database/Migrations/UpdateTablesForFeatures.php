<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTablesForFeatures extends Migration
{
    public function up()
    {
        // 1. Tambah Jam Kerja di Tabel Users
        $fieldsUsers = [
            'jam_kerja_start' => [
                'type' => 'TIME',
                'null' => true,
                'default' => '00:00:00',
                'after' => 'role'
            ],
            'jam_kerja_end' => [
                'type' => 'TIME',
                'null' => true,
                'default' => '23:59:59',
                'after' => 'jam_kerja_start'
            ],
        ];
        $this->forge->addColumn('users', $fieldsUsers);

        // 2. Tambah Snapshot Harga Beli di Detail Penjualan
        $fieldsDetail = [
            'harga_beli_snapshot' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'jumlah',
                'comment'    => 'HPP saat barang terjual'
            ],
        ];
        $this->forge->addColumn('detail_penjualan', $fieldsDetail);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['jam_kerja_start', 'jam_kerja_end']);
        $this->forge->dropColumn('detail_penjualan', 'harga_beli_snapshot');
    }
}