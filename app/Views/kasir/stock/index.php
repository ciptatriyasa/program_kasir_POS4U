<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Stok Barang</h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            <p><?= session()->getFlashdata('success') ?></p>
        </div>
    <?php endif; ?>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2">
            
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-list-check mr-2 text-blue-600"></i> Daftar Stok Produk
            </h2>
            <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg mb-8">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($produk)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Belum ada data produk.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($produk as $p): ?>
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-medium"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $fotoName = $p['foto_produk'];
                                            $placeholder = base_url('assets/images/placeholder_product.png');
                                            $fotoUrl = !empty($fotoName) ? base_url('uploads/produk/' . $fotoName) . '?v=' . time() : $placeholder;
                                        ?>
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded object-cover border border-gray-200" 
                                                 src="<?= $fotoUrl ?>" 
                                                 alt="Foto" 
                                                 onerror="this.onerror=null;this.src='<?= $placeholder ?>';">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <?= esc($p['nama_produk']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?= esc($p['nama_kategori'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold <?= $p['is_low'] ? 'text-orange-600' : ($p['stok'] == 0 ? 'text-red-600' : 'text-green-600') ?>">
                                        <?= esc($p['stok']) ?> pcs
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            // PERBAIKAN LOGIKA STATUS BADGE
                                            if ($p['stok'] == 0) {
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Habis</span>';
                                            } elseif ($p['is_low']) {
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menipis</span>';
                                            } else {
                                                echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aman</span>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-clock-rotate-left mr-2 text-purple-600"></i> Riwayat Permintaan Stok Saya
            </h2>
            <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Belum ada riwayat permintaan stok terbaru.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $noRiwayat = 1; foreach ($riwayat as $req): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium"><?= $noRiwayat++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?= esc(date('d M Y', strtotime($req['created_at']))) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= esc($req['nama_produk'] ?? 'Produk Dihapus') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?= esc($req['nama_supplier'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                        <?= esc($req['jumlah_diminta']) ?> pcs
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $statusClass = [
                                                'pending'  => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ][$req['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize <?= $statusClass ?>">
                                            <?= esc($req['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                        <?= esc($req['alasan']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-1" x-data="stockRequestForm(<?= htmlspecialchars(json_encode($suppliers), ENT_QUOTES, 'UTF-8') ?>)">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-cart-arrow-down mr-2 text-green-600"></i> Permintaan Stok
            </h2>
            <div class="bg-white p-5 shadow-md sm:rounded-lg sticky top-6 border border-gray-100">
                <p class="text-sm text-gray-600 mb-4">
                    Pilih supplier, produk yang dibutuhkan, dan jumlahnya untuk dikirim ke admin.
                </p>
                <form action="<?= base_url('kasir/stock/save') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="space-y-4">
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
                            <label class="block text-sm font-medium text-gray-700">Pilih Produk (Tersedia di Supplier)</label>
                            <select name="id_produk" x-model="selectedProduct" @change="updateMaxStock()" :disabled="!selectedSupplier" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                                <option value="">-- Pilih Produk --</option>
                                <template x-for="prod in products" :key="prod.id_produk">
                                    <option :value="prod.id_produk" 
                                            x-text="prod.nama_produk + ' (Stok Supplier: ' + prod.stok_tersedia + ')'" 
                                            :data-max-stock="prod.stok_tersedia">
                                </option>
                            </template>
                        </select>
                        <p x-show="selectedSupplier && products.length === 0" class="mt-1 text-xs text-red-500">Tidak ada produk tersedia di katalog.</p>
                        <p x-show="maxStock > 0" class="mt-1 text-xs text-blue-600">
                            Stok Maksimum yang dapat diminta: <span class="font-bold" x-text="maxStock"></span> pcs.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah Diminta</label>
                        <input type="number" name="jumlah_diminta" x-model.number="requestedQty" @input="validateQty" :max="maxStock" :disabled="!selectedProduct" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alasan</label>
                        <textarea name="alasan" rows="2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                </div>
                
                <button type="submit" :disabled="isSubmitDisabled()" class="w-full mt-6 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 font-medium disabled:bg-gray-400">
                    Kirim Permintaan ke Admin
                </button>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('stockRequestForm', (initialSuppliers) => ({
                suppliers: initialSuppliers,
                products: [],
                selectedSupplier: '<?= old('id_supplier') ?>' || '',
                selectedProduct: '<?= old('id_produk') ?>' || '',
                maxStock: 0,
                requestedQty: <?= old('jumlah_diminta') !== null ? (int)old('jumlah_diminta') : 'null' ?>, 

                init() {
                    if (this.selectedSupplier) {
                        this.fetchProducts().then(() => {
                            this.updateMaxStock();
                            if ('<?= session()->getFlashdata('validation') ?>') {
                                this.validateQty();
                            }
                        });
                    }
                },

                fetchProducts() {
                    this.products = [];
                    this.maxStock = 0;
                    this.selectedProduct = '';
                    if(!this.selectedSupplier) return Promise.resolve();

                    return fetch(`<?= base_url('kasir/api/supplier-products') ?>/${this.selectedSupplier}`, {
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.products = data;
                        if (this.selectedProduct && !this.products.find(p => p.id_produk == this.selectedProduct)) {
                            this.selectedProduct = '';
                        }
                    });
                },

                updateMaxStock() {
                    if (!this.selectedProduct) {
                        this.maxStock = 0;
                        return;
                    }
                    const selected = this.products.find(p => p.id_produk == this.selectedProduct);
                    this.maxStock = selected ? parseInt(selected.stok_tersedia) : 0;
                    this.validateQty();
                },

                validateQty() {
                    let qty = parseInt(this.requestedQty);
                    if (isNaN(qty) || this.requestedQty === null || this.requestedQty === '') return;
                    
                    if (qty > this.maxStock && this.maxStock > 0 && this.selectedProduct) {
                        this.requestedQty = this.maxStock; 
                        Swal.fire({
                            icon: 'error',
                            title: 'Permintaan Melebihi Stok!',
                            text: `Jumlah permintaan (${qty} pcs) melebihi stok yang tersedia di supplier (maksimum ${this.maxStock} pcs). Jumlah direset ke ${this.maxStock}.`,
                            confirmButtonText: 'Oke',
                            confirmButtonColor: '#d33'
                        });
                    }
                },

                isSubmitDisabled() {
                    return !this.selectedProduct || parseInt(this.requestedQty) <= 0 || isNaN(parseInt(this.requestedQty));
                }
            }))
        });
    </script>
<?= $this->endSection() ?>