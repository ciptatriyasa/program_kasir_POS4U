<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    
    <div x-data="{ 
        editModalOpen: false, 
        editData: { id: null, nama: '', harga: 0, stok: 0, exp: '' },
        openEdit(item) {
            // Logika untuk menangani nama item atau nama produk yang terhubung
            let displayName = item.nama_item ? item.nama_item : item.nama_produk;
            
            this.editData = {
                id: item.id,
                nama: displayName, 
                harga: item.harga_dari_supplier,
                stok: item.stok_tersedia,
                exp: item.exp_date ? item.exp_date : '' // Handle jika null
            };
            this.editModalOpen = true;
        }
    }">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Katalog</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Supplier: <span class="font-semibold text-blue-600"><?= esc($supplier['nama_supplier']) ?></span>
                    (Kategori: <?= esc($supplier['nama_kategori'] ?? 'Umum') ?>)
                </p>
            </div>
            <a href="<?= base_url('admin/suppliers') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
                Kembali
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fa-solid fa-check-circle"></i></div>
                    <div class="ml-3"><p><?= session()->getFlashdata('success') ?></p></div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fa-solid fa-exclamation-circle"></i></div>
                    <div class="ml-3"><p><?= session()->getFlashdata('error') ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1">
                <div class="bg-white shadow-md sm:rounded-lg overflow-hidden sticky top-6 border border-gray-100">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fa-solid fa-plus-circle text-blue-500 mr-2"></i> Tambah Produk Jualan
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 ml-6">Masukkan barang baru ke katalog.</p>
                    </div>
                    
                    <div class="p-6">
                        <form action="<?= base_url('admin/suppliers/catalog/' . $supplier['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                                    <input type="text" name="nama_item" required 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                           placeholder="Contoh: Kopi Sachet">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Supplier</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="harga_dari_supplier" required min="0" 
                                               class="block w-full rounded-md border-gray-300 pl-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                               placeholder="0">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Tersedia</label>
                                    <input type="number" name="stok_tersedia" required min="0" value="0" 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Exp Date (Opsional)</label>
                                    <input type="date" name="exp_date" 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i class="fa-solid fa-save mr-2 mt-0.5"></i> Simpan ke Katalog
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white shadow-md overflow-hidden sm:rounded-lg border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($catalog)): ?>
                                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 italic">Belum ada item di katalog ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($catalog as $item): ?>
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if($item['id_produk']): ?>
                                                    <div class="text-sm font-bold text-green-700 flex items-center">
                                                        <i class="fa-solid fa-link mr-1.5" title="Terhubung ke Sistem"></i> <?= esc($item['nama_produk']) ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500 ml-5">Linked to System</div>
                                                <?php else: ?>
                                                    <div class="text-sm font-medium text-gray-900"><?= esc($item['nama_item']) ?></div>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                                        Manual Input
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                                Rp <?= number_format($item['harga_dari_supplier'], 0, ',', '.') ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php if ($item['exp_date']): ?>
                                                    <span class="<?= (strtotime($item['exp_date']) < time()) ? 'text-red-600 font-bold' : '' ?>">
                                                        <?= date('d M Y', strtotime($item['exp_date'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= esc($item['stok_tersedia']) ?> pcs
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <button @click='openEdit(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)' 
                                                        class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </button>
                                                
                                                <form action="<?= base_url('admin/suppliers/catalog/delete/' . $item['id']) ?>" method="post" class="inline" onsubmit="return confirm('Hapus item ini dari katalog?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <template x-teleport="body">
            <div class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="editModalOpen" style="display: none;">
                
                <div x-show="editModalOpen" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                     @click="editModalOpen = false"></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        
                        <div x-show="editModalOpen" 
                             @click.outside="editModalOpen = false"
                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-200">
                            
                            <form :action="'<?= base_url('admin/suppliers/catalog/update/') ?>' + editData.id" method="post">
                                <?= csrf_field() ?>
                                
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <i class="fa-solid fa-edit text-blue-600 text-lg"></i>
                                        </div>
                                        
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                                Edit Stok & Harga
                                            </h3>
                                            <div class="mt-1">
                                                <p class="text-sm text-gray-500">
                                                    Produk: <span x-text="editData.nama" class="font-bold text-gray-800"></span>
                                                </p>
                                            </div>
                                            
                                            <div class="mt-5 space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga dari Supplier</label>
                                                    <div class="relative rounded-md shadow-sm">
                                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                                        </div>
                                                        <input type="number" name="harga_dari_supplier" x-model="editData.harga" required min="0" 
                                                               class="block w-full rounded-md border-gray-300 pl-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Tersedia</label>
                                                    <input type="number" name="stok_tersedia" x-model="editData.stok" required min="0" 
                                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expired Date</label>
                                                    <input type="date" name="exp_date" x-model="editData.exp" 
                                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row-reverse gap-3 sm:gap-4 sm:px-6">
    
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:w-auto transition-colors">
                                        Simpan Perubahan
                                    </button>

                                    <button type="button" @click="editModalOpen = false" class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition-colors">
                                        Batal
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>
<?= $this->endSection() ?>