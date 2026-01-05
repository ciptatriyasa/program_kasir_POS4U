<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Detail Supplier') ?></h1>
        <a href="<?= base_url('admin/suppliers') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?= esc($supplier['nama_supplier']) ?>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    ID Supplier: #<?= esc($supplier['id']) ?>
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="<?= base_url('admin/suppliers/catalog/' . $supplier['id']) ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <i class="fa-solid fa-list mr-2"></i> Kelola Katalog
                </a>
                <a href="<?= base_url('admin/suppliers/edit/' . $supplier['id']) ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fa-solid fa-pencil mr-2"></i> Edit
                </a>
            </div>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Kategori Spesialisasi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            <?= esc($supplier['nama_kategori'] ?? 'Tidak ada kategori') ?>
                        </span>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($supplier['email'] ?: '-') ?>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($supplier['no_telp'] ?: '-') ?>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= nl2br(esc($supplier['alamat'] ?: '-')) ?>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Terdaftar Sejak</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc(date('d M Y, H:i', strtotime($supplier['created_at']))) ?>
                    </dd>
                </div>

            </dl>
        </div>
    </div>
<?= $this->endSection() ?>