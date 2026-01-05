<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    
    <?php 
        // 1. PHP Logic for Category Cards (DIPERTAHANKAN)
        $allCategory = [
            'id' => '',
            'nama_kategori' => 'Semua',
            'icon' => 'fa-th-large'
        ];
        
        // Mapping Kategori dengan Icon yang Lebih Sesuai
        $iconMapping = [
            'Minuman' => 'fa-mug-hot',
            'Snack' => 'fa-cookie-bite',
            'ATK' => 'i class="fa-solid fa-book',
        ];

        $kategoriWithIcons = array_map(function($kat) use ($iconMapping) {
            $nama = esc($kat['nama_kategori']);
            $kat['icon'] = $iconMapping[$nama] ?? 'fa-tag';
            return $kat;
        }, $kategori);
        
        $displayKategori = array_merge([$allCategory], $kategoriWithIcons);
        
        $shiftWarning = session()->getFlashdata('shift_warning');
    ?>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Point of Sale</h1>
        <div class="text-sm text-gray-500 flex items-center gap-4">
            
            <div class="flex items-center">
                <span class="font-semibold text-gray-700 hidden sm:inline mr-2" id="liveDate"><?= date('D, d M Y') ?></span>
                <span class="font-mono font-bold text-xl text-blue-600" id="liveClockTime"><?= date('H:i:s') ?></span>
            </div>

            <a href="<?= base_url('kasir/shift/close') ?>" class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                <i class="fa-solid fa-power-off mr-2"></i> Tutup Shift
            </a>
        </div>
    </div>

    <?php if ($shiftWarning): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-6 shadow-md border-r-2" role="alert">
            <div class="flex items-center">
                <i class="fa-solid fa-exclamation-triangle mr-3 text-yellow-600 text-xl"></i>
                <div>
                    <p class="font-bold">PERINGATAN SHIFT!</p>
                    <p class="text-sm"><?= esc($shiftWarning) ?></p>
                    <a href="<?= base_url('kasir/shift/close') ?>" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                        <i class="fa-solid fa-lock mr-2"></i> Tutup Shift Sekarang
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div x-data="posApp()" class="lg:grid lg:grid-cols-5 lg:gap-6 h-full pb-20 lg:pb-0 items-start">
        <?= csrf_field() ?> 

        <div class="lg:col-span-3 flex flex-col h-full">

            <div class="bg-white rounded-xl shadow-sm p-4 mb-4 border border-gray-100 sticky top-0 z-10">
                <div class="relative flex-1 mb-4">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchProduct()" autofocus
                           placeholder="Cari nama barang atau scan barcode..."
                           class="block w-full pl-12 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition duration-150 ease-in-out">
                </div>
                
                <div class="flex overflow-x-auto gap-3 pb-2 custom-scrollbar">
                    <?php foreach ($displayKategori as $kat): ?>
                    <button @click="selectedCategory = '<?= esc($kat['id']) ?>'"
                            :class="selectedCategory === '<?= esc($kat['id']) ?>' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="flex-shrink-0 flex flex-col items-center justify-center p-3 w-20 h-20 rounded-xl transition duration-150 text-xs font-semibold">
                        
                        <i class="fa-solid <?= esc($kat['icon']) ?> text-xl mb-1"></i>
                        <span class="line-clamp-1"><?= esc($kat['nama_kategori']) ?></span>
                        <span class="text-[10px] opacity-70 mt-1" x-text="countProducts('<?= esc($kat['id']) ?>') + ' item'"></span>
                    </button>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar" style="max-height: calc(100vh - 220px);">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <div @click="addToCart(p)" 
                             class="group bg-white rounded-xl shadow-sm hover:shadow-md cursor-pointer transition-all duration-200 border border-gray-200 overflow-hidden relative flex flex-col h-full min-h-[175px]">
                            
                            <div class="w-full h-32 bg-white p-1 flex items-center justify-center relative"> 
                                <img :src="p.foto_produk ? '<?= base_url('uploads/produk/') ?>' + p.foto_produk : '<?= base_url('assets/images/placeholder_product.png') ?>'" 
                                     alt="Produk" 
                                     class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"
                                     @error="$el.src='<?= base_url('assets/images/placeholder_product.png') ?>'">
                                
                                <div x-show="p.stok <= 0" class="absolute inset-0 bg-white/70 flex items-center justify-center backdrop-blur-[1px]">
                                    <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full uppercase tracking-wider shadow-sm">Habis</span>
                                </div>
                                
                                <div x-show="p.is_low && p.stok > 0" class="absolute top-1 left-1 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">
                                    Menipis!
                                </div>
                            </div>

                            <div class="p-2 flex flex-col flex-grow justify-between border-t border-gray-100 bg-gray-50">
                                
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight flex-1" x-text="p.nama_produk" :title="p.nama_produk"></h3>
                                    
                                    <div x-show="p.posisi_rak" class="flex-shrink-0 ml-1">
                                        <p class="font-bold text-gray-700 bg-gray-200 px-1 py-0.5 rounded-sm whitespace-nowrap text-[10px] leading-none">
                                            Rak: <span x-text="p.posisi_rak"></span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    
                                    <div class="flex justify-between items-center text-[10px]">
                                        <p class="text-gray-600 font-medium whitespace-nowrap">Stok: <span x-text="p.stok"></span></p>
                                        <p class="font-bold text-blue-600 text-xs whitespace-nowrap" x-text="'Rp ' + formatRupiah(p.harga)"></p>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="absolute inset-0 border-2 border-blue-500 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none rounded-xl"></div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-64 text-center text-gray-500">
                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                        <i class="fa-solid fa-box-open text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-600">Tidak ada produk ditemukan</p>
                    <p class="text-sm">Coba kata kunci lain atau ubah filter kategori.</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 flex flex-col h-full bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mt-4 lg:mt-0">
            
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 flex items-center text-lg">
                    <i class="fa-solid fa-cart-shopping mr-2 text-blue-600"></i>
                    Keranjang <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full" x-text="cart.length"></span>
                </h3>
                <button @click="clearCart()" x-show="cart.length > 0" class="text-xs text-red-600 hover:text-red-800 font-medium flex items-center transition bg-white px-2 py-1 rounded border border-red-200 hover:bg-red-50">
                    <i class="fa-solid fa-trash-can mr-1"></i> Reset
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-white custom-scrollbar" style="max-height: calc(100vh - 420px);">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition group shadow-sm">
                        
                        <div class="flex-1 overflow-hidden mr-3">
                            <p class="text-sm font-bold text-gray-800 truncate" x-text="item.name"></p>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="formatRupiah(item.price) + ' / pcs'"></p>
                        </div>
                        
                        <div class="flex items-center gap-2 mr-3">
                            <button @click="updateQty(item, -1)" 
                                    class="w-9 h-9 flex items-center justify-center bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 rounded-lg transition shadow-sm">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            
                            <input type="number" x-model.number="item.qty" @change="manualQty(item)" 
                                   class="w-12 h-9 text-center text-sm font-bold border border-gray-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 p-0 text-gray-700 shadow-sm">
                            
                            <button @click="updateQty(item, 1)" 
                                    class="w-9 h-9 flex items-center justify-center bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition shadow-sm">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>

                        <div class="text-right flex flex-col items-end justify-center w-24">
                            <p class="text-sm font-bold text-gray-800" x-text="'Rp ' + formatRupiah(item.subtotal)"></p>
                            
                            <button @click="removeFromCart(item.id)" 
                                    class="mt-1 text-red-400 hover:text-red-600 transition flex items-center text-[11px] px-2 py-1 rounded hover:bg-red-50"
                                    title="Hapus item ini">
                                <i class="fa-solid fa-trash-alt mr-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-full text-center py-10 opacity-60">
                    <div class="bg-gray-50 p-4 rounded-full mb-3">
                        <i class="fa-solid fa-cart-arrow-down text-3xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Keranjang Kosong</p>
                    <p class="text-xs text-gray-400 mt-1">Pilih produk di sebelah kiri.</p>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-5 border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600 font-medium">Total Tagihan</span>
                    <span class="text-2xl font-extrabold text-gray-900 tracking-tight" x-text="'Rp ' + formatRupiah(totalAmount)"></span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Metode Bayar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-wallet text-gray-400 text-lg"></i>
                            </div>
                            <select x-model="paymentMethod" class="w-full pl-12 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-2.5 bg-white font-medium text-gray-700">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS / E-Wallet</option>
                                <option value="card">Kartu Debit/Kredit</option>
                            </select>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'cash'">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Uang Diterima</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold sm:text-sm">Rp</span>
                            </div>
                            <input type="number" x-model.number="paidAmount" @keydown.enter.prevent="processSale($event)" 
                                   class="w-full pl-12 text-sm border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 py-2.5 font-bold text-gray-800" placeholder="0">
                        </div>
                    </div>
                </div>

                <div x-show="paymentMethod === 'cash'" x-transition class="flex justify-between items-center mb-4 px-3 py-2 rounded-lg border-2"
                     :class="changeAmount >= 0 ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300'">
                    <span class="text-base font-medium text-gray-700">Kembalian</span>
                    <span class="text-3xl font-extrabold" 
                          :class="changeAmount >= 0 ? 'text-green-700' : 'text-red-700'" 
                          x-text="'Rp ' + formatRupiah(changeAmount)">
                    </span>
                </div>
                
                <button type="button" 
                        @click.prevent="processSale($event)" 
                        :disabled="isPaymentDisabled()" 
                        x-ref="paymentButton"
                        :class="isPaymentDisabled() ? 'bg-gray-400 text-gray-100 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-lg hover:shadow-xl'"
                        class="w-full py-4 rounded-xl font-bold transition-all duration-200 transform active:scale-[0.98] flex justify-center items-center gap-2 text-lg">
                    <i class="fa-solid" :class="isPaymentDisabled() ? 'fa-ban' : 'fa-print'"></i> 
                    <span x-text="paymentButtonText()"></span>
                </button>
            </div>
        </div>

    </div>

    <script>
        // Update Live Clock and Date
        function updateLiveHeader() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateString = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
            
            const timeElement = document.getElementById('liveClockTime');
            const dateElement = document.getElementById('liveDate');
            
            if (timeElement) timeElement.innerText = timeString;
            if (dateElement) dateElement.innerText = dateString;
        }
        setInterval(updateLiveHeader, 1000);
        updateLiveHeader(); // Initial run

        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                products: <?= json_encode($produk) ?>,
                cart: [],
                searchQuery: '',
                selectedCategory: '', 
                paidAmount: 0,
                paymentMethod: 'cash',
                csrfToken: document.querySelector('input[name="<?= csrf_token() ?>"]').value,

                init() {},

                updateCsrfToken(newHash) {
                    if (newHash) {
                        this.csrfToken = newHash;
                        document.querySelector('input[name="<?= csrf_token() ?>"]').value = newHash;
                    }
                },
                
                countProducts(categoryId) {
                    if (!categoryId) return this.products.length;
                    return this.products.filter(p => p.id_kategori == categoryId).length;
                },

                refreshProducts() {
                    fetch('<?= base_url('/kasir/products-api') ?>', {
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrfToken 
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.products = data.products;
                        this.updateCsrfToken(data.csrf_hash); 
                        document.querySelector('input[placeholder="Cari nama barang atau scan barcode..."]').focus();
                    })
                    .catch(err => {
                        console.error('Gagal refresh produk:', err);
                        Swal.fire('Error', 'Gagal memuat ulang data produk', 'error');
                    });
                },

                get filteredProducts() {
                    return this.products.filter(p => {
                        const matchesCategory = !this.selectedCategory || p.id_kategori == this.selectedCategory; 
                        const matchesSearch = !this.searchQuery || 
                                              p.nama_produk.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                              (p.barcode && p.barcode.toLowerCase().includes(this.searchQuery.toLowerCase()));
                        return matchesCategory && matchesSearch;
                    });
                },

                get totalAmount() {
                    return this.cart.reduce((sum, item) => sum + item.subtotal, 0);
                },

                get changeAmount() {
                    if (this.paymentMethod !== 'cash') return 0;
                    return Math.floor(this.paidAmount - this.totalAmount); 
                },

                paymentButtonText() {
                    if (this.cart.length === 0) return 'Keranjang Kosong';
                    if (this.paymentMethod === 'cash' && this.paidAmount < this.totalAmount) return 'Uang Kurang';
                    return 'Bayar Sekarang';
                },

                isPaymentDisabled() {
                    if (this.cart.length === 0) return true;
                    if (this.paymentMethod === 'cash') return this.changeAmount < 0 || this.paidAmount <= 0; 
                    return false;
                },

                formatRupiah(number) {
                    let num = parseFloat(number);
                    if (isNaN(num)) return '0';
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(Math.abs(num));
                },

                addToCart(product) {
                     if (product.stok <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok Habis',
                            text: `Produk ${product.nama_produk} sudah habis.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        return;
                    }

                    const existingItem = this.cart.find(item => item.id === product.id);
                    
                    if (existingItem) {
                        if (existingItem.qty < product.stok) {
                            existingItem.qty++;
                            existingItem.subtotal = existingItem.qty * existingItem.price;
                            
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: false
                            });
                            Toast.fire({ icon: 'success', title: '+1 ' + product.nama_produk });

                        } else {
                            Swal.fire('Batas Stok', `Maksimal stok tersedia: ${product.stok} pcs`, 'warning');
                        }
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.nama_produk,
                            price: parseFloat(product.harga),
                            qty: 1,
                            max_stok: product.stok,
                            subtotal: parseFloat(product.harga),
                            posisi_rak: product.posisi_rak
                        });
                    }
                },
                
                searchProduct() {
                    if (!isNaN(this.searchQuery) && this.searchQuery.length > 0) {
                        const found = this.products.find(p => p.barcode === this.searchQuery && p.stok > 0);
                        if (found) {
                            this.addToCart(found);
                            this.searchQuery = '';
                            return;
                        } else {
                            Swal.fire('Tidak Ditemukan', 'Produk dengan barcode tersebut tidak ditemukan atau stok habis.', 'info');
                        }
                    }
                    document.activeElement.blur();
                },

                updateQty(item, amount) {
                     let newQty = item.qty + amount;
                    if (newQty < 1) return;
                    
                    if (newQty > item.max_stok) {
                        Swal.fire('Stok Tidak Cukup', `Sisa stok hanya ${item.max_stok}`, 'warning');
                        item.qty = item.max_stok;
                    } else {
                        item.qty = newQty;
                    }
                    item.subtotal = item.qty * item.price;
                },

                manualQty(item) {
                    if (item.qty < 1 || isNaN(item.qty)) item.qty = 1;
                    if (item.qty > item.max_stok) {
                        Swal.fire('Stok Tidak Cukup', `Sisa stok hanya ${item.max_stok}`, 'warning');
                        item.qty = item.max_stok;
                    }
                    item.subtotal = item.qty * item.price;
                },

                removeFromCart(id) {
                    this.cart = this.cart.filter(item => item.id !== id);
                },

                clearCart() {
                     Swal.fire({
                        title: 'Kosongkan Keranjang?',
                        text: "Semua item akan dihapus dari transaksi ini.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Kosongkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.cart = [];
                            this.paidAmount = 0;
                            this.paymentMethod = 'cash';
                            Swal.fire('Dikosongkan!', 'Keranjang belanja bersih.', 'success');
                        }
                    })
                },

                processSale(event) {
                     if (this.isPaymentDisabled()) {
                        Swal.fire('Gagal', 'Periksa kembali keranjang atau nominal pembayaran.', 'error');
                        return;
                    }
                    
                    const button = this.$refs.paymentButton;
                    const originalContent = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';
                    
                    const cartData = this.cart.map(item => ({ id: item.id, qty: item.qty }));
                    const saleData = new FormData();
                    saleData.append('<?= csrf_token() ?>', this.csrfToken);
                    saleData.append('cart_items', JSON.stringify(cartData));
                    saleData.append('metode_pembayaran', this.paymentMethod);
                    saleData.append('uang_dibayar', this.paymentMethod === 'cash' ? this.paidAmount : this.totalAmount);
                    saleData.append('kembalian', this.paymentMethod === 'cash' ? this.changeAmount : 0);
                    
                    fetch('<?= base_url('/kasir/transaksi/process') ?>', {
                        method: 'POST',
                        body: saleData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.updateCsrfToken(data.csrf_hash);

                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Transaksi Berhasil!',
                                html: `
                                    <div class="text-center">
                                        <div class="text-4xl text-green-500 mb-2"><i class="fa-regular fa-circle-check"></i></div>
                                        <p class="text-lg font-bold">Kembalian: Rp ${this.formatRupiah(this.changeAmount)}</p>
                                        <p class="text-sm text-gray-500 mt-1">Invoice #${data.invoice_id}</p>
                                    </div>
                                `,
                                showCancelButton: true,
                                confirmButtonText: '<i class="fa-solid fa-print"></i> Cetak Struk',
                                cancelButtonText: 'Transaksi Baru',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#6c757d',
                                reverseButtons: true
                            }).then((result) => {
                                
                                this.refreshProducts();

                                if (result.isConfirmed) {
                                    window.open('<?= base_url('/kasir/transaksi/receipt/') ?>' + data.invoice_id, '_blank', 'width=300,height=600,scrollbars=yes,resizable=yes');
                                }

                                // Reset state AlpineJS setelah SweetAlert ditutup
                                this.cart = [];
                                this.paidAmount = 0;
                                this.paymentMethod = 'cash';
                            });
                            
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error Sistem', 'Terjadi kesalahan koneksi.', 'error');
                    })
                    .finally(() => {
                        button.disabled = false;
                        button.innerHTML = originalContent;
                    });
                }
            }))
        });
    </script>
<?= $this->endSection() ?>