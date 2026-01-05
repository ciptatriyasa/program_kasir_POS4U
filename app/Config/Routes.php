<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. AUTHENTICATION ROUTES
// --------------------------------------------------------------------
// Mengarah ke folder Controllers/Auth
// Login
$routes->get('/', 'Auth\Login::index');
$routes->get('login', 'Auth\Login::index');
$routes->post('login', 'Auth\Login::process');
$routes->get('logout', 'Auth\Login::logout');

// Register
$routes->get('register', 'Auth\Register::index');
$routes->post('register', 'Auth\Register::save');


// --------------------------------------------------------------------
// 2. DASHBOARD GATEKEEPER
// --------------------------------------------------------------------
// Controller ini bertugas mengarahkan user ke Admin atau Kasir sesuai role
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);


// --------------------------------------------------------------------
// 3. PROFIL USER (SHARED)
// --------------------------------------------------------------------
// Bisa diakses oleh Admin maupun Kasir
$routes->group('profile', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::update');
});


// --------------------------------------------------------------------
// 4. MODUL KASIR
// --------------------------------------------------------------------
// Namespace: App\Controllers\Kasir
$routes->group('kasir', ['filter' => ['auth'], 'namespace' => 'App\Controllers\Kasir'], static function ($routes) {
    
    $routes->get('/', 'Dashboard::index');      // Dashboard Kasir
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('pos', 'Dashboard::pos');      // Mesin Kasir (Jualan)
    
    // API Helper untuk Load Produk di POS
    $routes->get('products-api', 'Dashboard::getProductsApi');

    // Fitur Transaksi (Proses Bayar & Struk)
    // Mengarah ke controller Transaksi.php
    $routes->post('transaksi/process', 'Transaksi::process');
    $routes->get('transaksi/receipt/(:num)', 'Transaksi::receipt/$1');

    // Fitur Permintaan Stok (Sudah diubah ke 'stock')
    // Mengarah ke controller Stock.php
    $routes->get('stock', 'Stock::index');
    $routes->post('stock/save', 'Stock::save');
    
    // API Helper untuk Dropdown Supplier di halaman Stok
    $routes->get('api/suppliers', 'Stock::getSuppliersApi');
    $routes->get('api/supplier-products/(:num)', 'Stock::getSupplierProductsApi/$1');

    // --- TAMBAHKAN FITUR RIWAYAT TRANSAKSI KASIR ---
    // Mengarah ke controller Riwayat.php (Baru)
    $routes->get('riwayat', 'Riwayat::index');
    $routes->get('riwayat/detail-api/(:num)', 'Riwayat::getDetailApi/$1'); 
    // -----------------------------------------------

    // --- TAMBAHKAN INI (MANAJEMEN SHIFT) ---
    $routes->get('shift/open', 'Shift::open');
    $routes->post('shift/open', 'Shift::processOpen');
    $routes->get('shift/close', 'Shift::close');
    $routes->post('shift/close', 'Shift::processClose');
});


// --------------------------------------------------------------------
// 5. MODUL ADMIN
// --------------------------------------------------------------------
// Namespace: App\Controllers\Admin
$routes->group('admin', ['filter' => ['auth', 'admin'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    
    // Dashboard Admin
    $routes->get('dashboard', 'Dashboard::index'); 

    // Manajemen User
    $routes->get('users', 'UserController::index');
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->get('users/show/(:num)', 'UserController::show/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->post('users/delete/(:num)', 'UserController::delete/$1');

    // --- [PERBAIKAN PENTING DI SINI] ---
    // Tambahkan rute spesifik agar JavaScript Modal bisa mengakses store & update
    $routes->post('kategori/store', 'KategoriController::store');
    $routes->post('kategori/update/(:num)', 'KategoriController::update/$1');
    
    // Resource route tetap ada untuk index & delete
    $routes->resource('kategori', [
        'controller' => 'KategoriController', 
        'except' => 'show'
    ]);
    // -----------------------------------

    // Manajemen Produk
    $routes->post('produk/update/(:num)', 'ProdukController::update/$1');
    $routes->get('produk/show/(:num)', 'ProdukController::show/$1');
    $routes->get('produk/download-barcodes', 'ProdukController::downloadAllBarcodes');
    $routes->get('produk/get-catalog-items/(:num)', 'ProdukController::getCatalogItemsBySupplier/$1');
    
    $routes->resource('produk', [
        'controller' => 'ProdukController', 
        'except' => 'show'
    ]);
    
    // Manajemen Penjualan (Riwayat Transaksi)
    $routes->get('penjualan', 'PenjualanController::index');
    $routes->get('penjualan/show/(:num)', 'PenjualanController::show/$1');
    $routes->post('penjualan/delete/(:num)', 'PenjualanController::delete/$1');

    // Verifikasi Stok
    $routes->get('stock-requests', 'StockRequestController::index');
    $routes->post('stock-requests/process/(:num)', 'StockRequestController::process/$1');
    
    // Laporan
    $routes->get('reports', 'ReportController::index');
    $routes->post('reports/generate', 'ReportController::generate');
    $routes->get('reports/profit-loss', 'ReportController::profitLoss');
    $routes->post('reports/profit-loss-data', 'ReportController::getProfitLossData');
    $routes->get('reports/cashier-performance', 'ReportController::cashierPerformance');

    // Manajemen Supplier & Katalog
    $routes->get('suppliers/catalog/(:num)', 'SupplierController::catalog/$1');
    $routes->post('suppliers/catalog/(:num)', 'SupplierController::addCatalogItem/$1');
    $routes->post('suppliers/catalog/update/(:num)', 'SupplierController::updateCatalogItem/$1');
    $routes->post('suppliers/catalog/delete/(:num)', 'SupplierController::deleteCatalogItem/$1');
    $routes->post('suppliers/sync/(:num)', 'SupplierController::syncCatalog/$1');

    // --- [PERBAIKAN: TAMBAHAN KHUSUS UNTUK AJAX SUPPLIER] ---
    // Rute ini diperlukan karena "resource" tidak membuat endpoint /create atau /update yang spesifik
    $routes->post('suppliers/create', 'SupplierController::create');       // Fix untuk AJAX Create
    $routes->post('suppliers/update/(:num)', 'SupplierController::update/$1'); // Fix untuk AJAX Update
    // ---------------------------------------------------------

    $routes->resource('suppliers', ['controller' => 'SupplierController']);
});