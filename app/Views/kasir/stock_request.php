<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Permintaan Stok ke Supplier</h1>
        <a href="<?= base_url('/dashboard') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg" x-data="stockRequestForm()">
        <form action="<?= base_url('kasir/stock-request/save') ?>" method="post">
            <?= csrf_field() ?>
            <div class="px-4 py-5 sm:p-6 grid grid-cols-1 gap-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Supplier</label>
                    <select name="id_supplier" x-model="selectedSupplier" @change="fetchProducts()" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih Supplier --</option>
                        <template x-for="sup in suppliers" :key="sup.id">
                            <option :value="sup.id" x-text="sup.nama_supplier"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Produk dari Katalog</label>
                    <select name="id_produk" x-model="selectedProduct" :disabled="!selectedSupplier" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                        <option value="">-- Pilih Produk --</option>
                        <template x-for="prod in products" :key="prod.id_produk">
                            <option :value="prod.id_produk" 
                                    x-text="prod.nama_produk + ' (Stok Supplier: ' + prod.stok_tersedia + ' | Rp ' + prod.harga_dari_supplier + ')'">
                            </option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Diminta</label>
                    <input type="number" name="jumlah_diminta" required min="1"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Alasan</label>
                    <textarea name="alasan" rows="2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Kirim Permintaan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('stockRequestForm', () => ({
                suppliers: [],
                products: [],
                selectedSupplier: '',
                selectedProduct: '',

                init() {
                    // Fetch suppliers on load
                    fetch('<?= base_url('kasir/api/suppliers') ?>', {
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    })
                    .then(res => res.json())
                    .then(data => this.suppliers = data);
                },

                fetchProducts() {
                    this.products = [];
                    this.selectedProduct = '';
                    if(!this.selectedSupplier) return;

                    fetch(`<?= base_url('kasir/api/supplier-products') ?>/${this.selectedSupplier}`, {
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    })
                    .then(res => res.json())
                    .then(data => this.products = data);
                }
            }))
        });
    </script>
<?= $this->endSection() ?>