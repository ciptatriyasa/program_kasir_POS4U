<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Detail Produk') ?></h1>
        <a href="<?= base_url('admin/produk') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-start space-x-4">
                <?php 
                    // PERBAIKAN: Gunakan URL langsung, biarkan onerror menangani jika file hilang
                    $defaultFoto = base_url('assets/images/placeholder_product.png');
                    
                    if (!empty($produk['foto_produk'])) {
                        $currentFoto = base_url('uploads/produk/' . $produk['foto_produk']) . '?v=' . time();
                    } else {
                        $currentFoto = $defaultFoto;
                    }
                ?>
                <div class="flex-shrink-0 w-20 h-20 rounded overflow-hidden border border-gray-200">
                    <img class="w-full h-full object-cover" src="<?= $currentFoto ?>" alt="Foto Produk"
                         onerror="this.onerror=null;this.src='<?= $defaultFoto ?>';">
                </div>
                <div>
                    <h3 class="text-xl leading-6 font-medium text-gray-900">
                        <?= esc($produk['nama_produk']) ?>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Barcode: <?= esc($produk['barcode'] ?? '-') ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            <?= esc($produk['nama_kategori'] ?? 'Tanpa Kategori') ?>
                        </span>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5 bg-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Harga Beli</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        Rp <?= number_format($produk['harga_beli'] ?? 0, 0, ',', '.') ?>
                    </dd>
                </div>
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Harga Jual</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold text-blue-600">
                        Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Stok Saat Ini</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($produk['stok']) ?> pcs
                    </dd>
                </div>
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Exp. Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($produk['exp_date'] ? date('d M Y', strtotime($produk['exp_date'])) : 'Tidak Ada') ?>
                    </dd>
                </div>
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Posisi Rak</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($produk['posisi_rak'] ?? '-') ?>
                    </dd>
                </div>
                <?php if (!empty($barcodeImage)): ?>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Gambar Barcode</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="mt-1 p-4 bg-white border border-gray-300 rounded-md inline-block">
                            <img src="<?= $barcodeImage ?>" alt="Barcode <?= esc($produk['barcode']) ?>">
                            <p class="text-center text-sm font-mono tracking-widest mt-1"><?= esc($produk['barcode']) ?></p>
                        </div>
                    </dd>
                </div>
                <?php endif; ?>

                 <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc(date('d M Y, H:i', strtotime($produk['created_at']))) ?>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc(date('d M Y, H:i', strtotime($produk['updated_at']))) ?>
                    </dd>
                </div>

            </dl>
        </div>
    </div>
<?= $this->endSection() ?>