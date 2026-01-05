<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="riwayatApp()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Riwayat Transaksi Saya') ?></h1>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form action="<?= base_url('kasir/riwayat') ?>" method="get" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full md:w-auto">
                <label for="filter_tanggal" class="block text-sm font-medium text-gray-700 mb-1">Filter Tanggal (Cari per hari):</label>
                <input type="date" name="filter_tanggal" id="filter_tanggal" 
                       value="<?= esc($filterDate ?? '') ?>"
                       class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            </div>
            
            <div class="flex-1 w-full md:w-auto">
                 <label for="search_id" class="block text-sm font-medium text-gray-700 mb-1">Cari ID Transaksi:</label>
                 <input type="text" name="search_id" id="search_id" placeholder="Cari ID Transaksi..."
                       value="<?= esc($searchId ?? '') ?>"
                       class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            </div>

            <div class="mt-auto">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fa-solid fa-search mr-2"></i> Cari
                </button>
            </div>
            <div class="mt-auto">
                <a href="<?= base_url('kasir/riwayat') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100" title="Reset Filter">
                    Reset
                </a>
            </div>
        </form>
    </div>


    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Trx</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($penjualan)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada riwayat transaksi ditemukan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($penjualan as $p): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600"><?= $no++ ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">#<?= esc($p['id']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?= esc(date('d M Y, H:i', strtotime($p['tanggal_penjualan']))) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            Rp <?= number_format($p['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize 
                                                <?= $p['metode_pembayaran'] == 'cash' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' ?>">
                                                <?= esc($p['metode_pembayaran']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button @click="openDetail(<?= $p['id'] ?>)" type="button" class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" title="Lihat Detail">
                                                <i class="fa-solid fa-eye"></i> Detail
                                            </button>
                                            <a href="<?= base_url('kasir/transaksi/receipt/' . $p['id']) ?>" target="_blank" class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-100" title="Cetak Struk">
                                                <i class="fa-solid fa-print"></i> Cetak
                                            </a>
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
        <div class="relative z-100" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="detailModalOpen" style="display: none;">
            
            <div x-show="detailModalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                 @click="detailModalOpen = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    
                    <div x-show="detailModalOpen" 
                         @click.outside="detailModalOpen = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-gray-200">
                        
                        <div class="bg-white px-4 py-5 sm:p-6 sm:pb-4 border-b">
                            <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title">
                                Detail Transaksi #<span x-text="detailData.penjualan.id"></span>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">Kasir: <span x-text="detailData.penjualan.kasir_nama"></span></p>
                        </div>
                        
                        <div class="bg-white p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="space-y-3">
                                <h4 class="font-bold text-gray-800 border-b pb-2">Ringkasan Pembayaran</h4>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Total Tagihan</span>
                                    <span class="text-lg font-bold text-blue-600">Rp <span x-text="detailData.penjualan.total_harga_formatted"></span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Metode Pembayaran</span>
                                    <span class="text-sm font-semibold capitalize" x-text="detailData.penjualan.metode_pembayaran"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Uang Dibayar</span>
                                    <span class="text-sm font-medium text-gray-800">Rp <span x-text="detailData.penjualan.uang_dibayar_formatted"></span></span>
                                </div>
                                <div class="flex justify-between border-t pt-2">
                                    <span class="text-base font-bold text-gray-700">Kembalian</span>
                                    <span class="text-base font-bold text-green-600">Rp <span x-text="detailData.penjualan.kembalian_formatted"></span></span>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-gray-800 border-b pb-2 mb-2">Item Terjual</h4>
                                <div class="max-h-60 overflow-y-auto space-y-2">
                                    <template x-for="item in detailData.detail" :key="item.id">
                                        <div class="flex justify-between text-sm border-b pb-1">
                                            <div class="flex-1 pr-2">
                                                <span class="font-medium text-gray-900" x-text="item.nama_produk || 'Produk Dihapus'"></span>
                                                <span class="block text-xs text-gray-500" x-text="item.jumlah + ' x Rp ' + formatRupiah(item.subtotal / item.jumlah)"></span>
                                            </div>
                                            <span class="font-semibold text-gray-800">Rp <span x-text="formatRupiah(item.subtotal)"></span></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row-reverse gap-3 sm:gap-4 sm:px-6">
                            <a :href="'<?= base_url('kasir/transaksi/receipt/') ?>' + detailData.penjualan.id" target="_blank"
                               class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto transition-colors">
                                <i class="fa-solid fa-print mr-2 mt-0.5"></i> Cetak Ulang Struk
                            </a>
                            <button type="button" @click="detailModalOpen = false" class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition-colors">
                                Tutup
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('riwayatApp', () => ({
                detailModalOpen: false,
                detailData: {
                    penjualan: { id: '', kasir_nama: '', total_harga_formatted: '', metode_pembayaran: '', uang_dibayar_formatted: '', kembalian_formatted: '' },
                    detail: []
                },

                formatRupiah(number) {
                    let num = parseFloat(number);
                    if (isNaN(num)) return '0';
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(num);
                },

                openDetail(id) {
                    this.detailModalOpen = true;
                    this.detailData = {
                        penjualan: { id: id, kasir_nama: 'Memuat...', total_harga_formatted: '...', metode_pembayaran: '...', uang_dibayar_formatted: '...', kembalian_formatted: '...' },
                        detail: []
                    };
                    
                    fetch(`<?= base_url('kasir/riwayat/detail-api/') ?>${id}`, {
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.detailData = data;
                        } else {
                            Swal.fire('Error', data.message, 'error');
                            this.detailModalOpen = false;
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal memuat data transaksi.', 'error');
                        this.detailModalOpen = false;
                    });
                }
            }))
        });
    </script>
</div>

<?= $this->endSection() ?>