<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Detail Penjualan') ?></h1>
        <a href="<?= base_url('admin/penjualan') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Informasi Transaksi
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Detail transaksi yang dilakukan oleh kasir.
            </p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">ID Transaksi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold"><?= esc($penjualan['id']) ?></dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?= esc(date('d M Y, H:i', strtotime($penjualan['tanggal_penjualan']))) ?></dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Nama Kasir</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?= esc($penjualan['nama_lengkap'] ?? 'N/A') ?></dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Total Pembayaran</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 text-xl font-bold text-blue-600">
                        Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 mb-4">Item Terjual</h2>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($detail)): ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada item detail untuk transaksi ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($detail as $d): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($d['nama_produk'] ?? 'Produk Dihapus') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700"><?= esc($d['jumlah']) ?> pcs</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            Rp <?= number_format($d['subtotal'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase">
                                    Grand Total
                                </td>
                                <td class="px-6 py-3 text-left text-sm font-bold text-gray-900">
                                    Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>