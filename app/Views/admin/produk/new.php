<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Tambah Produk') ?></h1>
        <a href="<?= base_url('admin/produk') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('validation')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Validasi Gagal:</p>
            <ul class="list-disc list-inside">
            <?php foreach (session()->getFlashdata('validation')->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow sm:rounded-lg" x-data="productForm()">
        <form action="<?= base_url('admin/produk/') ?>" method="post" enctype="multipart/form-data" @submit.prevent="submitForm">
            <?= csrf_field() ?>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- FOTO PRODUK (Kiri) -->
                    <div class="md:col-span-1">
                        <label for="foto_produk" class="block text-sm font-medium text-gray-700">Foto Produk (Wajib)</label>
                        <div class="mt-1 flex items-center">
                            <img id="fotoPreview" class="h-32 w-32 object-cover rounded border border-gray-300 mr-4" 
                                 src="<?= base_url('assets/images/placeholder_product.png') ?>" alt="Preview">
                            <label class="cursor-pointer bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Upload Foto
                                <input type="file" name="foto_produk" id="foto_produk" class="sr-only" 
                                       onchange="document.getElementById('fotoPreview').src = window.URL.createObjectURL(this.files[0])" required>
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">JPG/PNG, Max 2MB</p>
                    </div>

                    <!-- FORM DATA (Kanan) -->
                    <div class="md:col-span-1 space-y-5">
                        
                        <!-- 1. Pilih Kategori -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="id_kategori" x-model="selectedCategory" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat['id'] ?>"><?= esc($kat['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Pilih Supplier -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <select name="id_supplier" x-model="selectedSupplier" @change="fetchCatalogItems()" :disabled="!selectedCategory" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm disabled:bg-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Supplier --</option>
                                <template x-for="sup in filteredSuppliers" :key="sup.id">
                                    <option :value="sup.id" x-text="sup.nama_supplier"></option>
                                </template>
                            </select>
                        </div>

                        <!-- 3. PILIH ITEM KATALOG (WAJIB) -->
                        <div class="bg-blue-50 p-4 rounded-md border border-blue-200">
                            <label class="block text-sm font-bold text-blue-800 mb-1">
                                Pilih Produk dari Katalog Supplier <span class="text-red-500">*</span>
                            </label>
                            <select name="id_catalog_item" x-model="selectedCatalogItem" @change="fillData()" 
                                    :disabled="!selectedSupplier"
                                    class="block w-full border-blue-300 rounded-md shadow-sm sm:text-sm disabled:bg-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Produk --</option>
                                <template x-for="item in catalogItems" :key="item.id">
                                    <option :value="item.id" x-text="item.nama_item + ' (Rp ' + item.harga_dari_supplier + ')'"></option>
                                </template>
                            </select>
                            
                            <!-- Pesan jika katalog kosong -->
                            <div x-show="selectedSupplier && catalogItems.length === 0" class="mt-2 text-xs text-red-600">
                                Supplier ini belum memiliki item di katalog yang belum terdaftar.
                            </div>
                            
                            <p class="text-xs text-blue-600 mt-2">
                                <i class="fa-solid fa-info-circle"></i> 
                                Anda wajib memilih produk dari daftar ini. Data nama, harga beli, dan expired date akan terisi otomatis.
                            </p>
                        </div>

                        <!-- INPUT READ-ONLY (Auto-filled) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="nama_produk" x-model="namaProduk" readonly 
                                   class="mt-1 block w-full border-gray-300 bg-gray-100 text-gray-600 rounded-md shadow-sm sm:text-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Beli (Dari Supplier)</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-500 sm:text-sm">Rp</span>
                                <input type="number" name="harga_beli" x-model="hargaBeli" readonly 
                                       class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 bg-gray-100 text-gray-600 sm:text-sm cursor-not-allowed">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Expired Date</label>
                            <input type="date" name="exp_date" x-model="expDate" readonly 
                                   class="mt-1 block w-full border-gray-300 bg-gray-100 text-gray-600 rounded-md shadow-sm sm:text-sm cursor-not-allowed">
                        </div>

                        <!-- INPUT MANUAL (Bisa diedit) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">Rp</span>
                                <input type="number" name="harga" required min="0" 
                                       class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Stok Awal</label>
                                <input type="text" name="stok" value="0" readonly 
                                       class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed">
                                <p class="text-xs text-red-500 mt-1">Stok diisi via Request Kasir.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Posisi Rak</label>
                                <input type="text" name="posisi_rak" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: A1">
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productForm', () => ({
                suppliers: <?= json_encode($suppliers) ?>,
                selectedCategory: '',
                selectedSupplier: '',
                catalogItems: [],
                selectedCatalogItem: '',
                
                // Form Fields
                namaProduk: '',
                hargaBeli: '',
                expDate: '',

                get filteredSuppliers() {
                    if (!this.selectedCategory) return [];
                    return this.suppliers.filter(s => s.id_kategori == this.selectedCategory);
                },

                fetchCatalogItems() {
                    this.catalogItems = [];
                    this.selectedCatalogItem = '';
                    this.resetForm();

                    if (!this.selectedSupplier) return;

                    fetch(`<?= base_url('admin/produk/get-catalog-items') ?>/${this.selectedSupplier}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.catalogItems = data;
                    });
                },

                fillData() {
                    if (!this.selectedCatalogItem) {
                        this.resetForm();
                        return;
                    }
                    const item = this.catalogItems.find(i => i.id == this.selectedCatalogItem);
                    if (item) {
                        this.namaProduk = item.nama_item;
                        this.hargaBeli = item.harga_dari_supplier;
                        this.expDate = item.exp_date;
                    }
                },
                
                resetForm() {
                    this.namaProduk = '';
                    this.hargaBeli = '';
                    this.expDate = '';
                },

                submitForm(e) {
                    // Validasi Klien: Pastikan Catalog Item dipilih
                    if (!this.selectedCatalogItem) {
                        alert('Harap pilih produk dari katalog supplier terlebih dahulu!');
                        return;
                    }
                    e.target.submit();
                }
            }))
        })
    </script>
<?= $this->endSection() ?>