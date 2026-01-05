<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSupplierToStockRequests extends Migration
{
    public function up()
    {
        // Add id_supplier column to existing stock_requests table
        $fields = [
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Nullable for backward compatibility
                'after'      => 'id_user_kasir',
            ],
        ];
        $this->forge->addColumn('stock_requests', $fields);
        
        // Add FK
        $this->forge->addForeignKey('id_supplier', 'suppliers', 'id', 'CASCADE', 'SET NULL', 'stock_requests_id_supplier_foreign');
        $this->forge->processIndexes('stock_requests');
    }

    public function down()
    {
        $this->forge->dropForeignKey('stock_requests', 'stock_requests_id_supplier_foreign');
        $this->forge->dropColumn('stock_requests', 'id_supplier');
    }
}