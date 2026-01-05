<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <h1 class="text-3xl font-bold text-gray-900 mb-6"><?= esc($title ?? 'Laporan') ?></h1>

    <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
        <form action="" method="get">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end"> 
                
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
                    <select id="report_type" name="report_type" required
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md border">
                        <option value="" disabled <?= empty($reportType) ? 'selected' : '' ?>>-- Pilih Laporan --</option>
                        <option value="sales" <?= ($reportType ?? '') == 'sales' ? 'selected' : '' ?>>Laporan Penjualan</option>
                        <option value="products" <?= ($reportType ?? '') == 'products' ? 'selected' : '' ?>>Laporan Produk Terlaris</option>
                        <option value="profit_loss" <?= ($reportType ?? '') == 'profit_loss' ? 'selected' : '' ?>>Laporan Laba Rugi</option>
                        <option value="cashier_performance" <?= ($reportType ?? '') == 'cashier_performance' ? 'selected' : '' ?>>Laporan Kinerja Kasir</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="<?= esc($start_date) ?>" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="<?= esc($end_date) ?>" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Tampilkan Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($reportType) && !empty($results)): ?>
    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">
                Hasil: 
                <?php 
                    if ($reportType == 'sales') echo 'Laporan Penjualan';
                    elseif ($reportType == 'products') echo 'Laporan Produk Terlaris';
                    elseif ($reportType == 'profit_loss') echo 'Laporan Laba Rugi';
                    elseif ($reportType == 'cashier_performance') echo 'Laporan Kinerja Kasir';
                ?>
            </h3>
            <p class="text-sm text-gray-500">
                Periode: <?= esc(date('d M Y', strtotime($start_date))) ?> s/d <?= esc(date('d M Y', strtotime($end_date))) ?>
            </p>
        </div>
        
        <?php if ($reportType == 'sales'): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($results as $row): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#<?= esc($row['id']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc(date('d M Y H:i', strtotime($row['tanggal_penjualan']))) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc($row['kasir_nama']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right font-bold text-gray-700">TOTAL OMSET</td>
                            <td class="px-6 py-3 text-left font-bold text-gray-900">Rp <?= number_format($total ?? 0, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        
        <?php elseif ($reportType == 'products'): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Terjual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($results as $row): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($row['nama_produk']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc($row['total_jumlah']) ?> pcs</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Rp <?= number_format($row['total_subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-3 text-right font-bold text-gray-700">TOTAL ITEM</td>
                            <td class="px-6 py-3 text-left font-bold text-gray-900"><?= esc($total ?? 0) ?> pcs</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        <?php elseif ($reportType == 'profit_loss'): ?>
            <?php 
                $sumOmset = $summary['total_omset'] ?? 0;
                $sumProfit = $summary['total_profit'] ?? 0;
                $sumHpp = $summary['total_hpp'] ?? 0;
            ?>
            <div class="grid grid-cols-2 gap-4 p-6 bg-blue-50 border-b border-blue-100">
                <div class="text-center">
                    <p class="text-sm font-medium text-blue-600">Total Omset</p>
                    <p class="text-2xl font-bold text-blue-900">Rp <?= number_format($sumOmset, 0, ',', '.') ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-green-600">Total Laba Kotor</p>
                    <p class="text-2xl font-bold text-green-900">Rp <?= number_format($sumProfit, 0, ',', '.') ?></p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Omset (Jual)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">HPP (Modal)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Laba Kotor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc(date('d M Y', strtotime($row['tanggal']))) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc($row['total_transaksi']) ?> trx</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">Rp <?= number_format($row['omset'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 text-sm text-right text-red-600">(Rp <?= number_format($row['total_hpp'], 0, ',', '.') ?>)</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-green-600">Rp <?= number_format($row['laba_kotor'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="2" class="px-6 py-3 text-right font-bold text-gray-700">TOTAL PERIODE INI</td>
                            <td class="px-6 py-3 text-right font-bold text-gray-900">Rp <?= number_format($sumOmset, 0, ',', '.') ?></td>
                            <td class="px-6 py-3 text-right font-bold text-red-700">(Rp <?= number_format($sumHpp, 0, ',', '.') ?>)</td>
                            <td class="px-6 py-3 text-right font-bold text-green-700">Rp <?= number_format($sumProfit, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        <?php elseif ($reportType == 'cashier_performance'): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kasir</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Penghasilan (Omset)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($results as $row): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <?= esc($row['nama_lengkap'] ?? 'User Terhapus') ?>
                                <br>
                                <span class="text-xs text-gray-500">@<?= esc($row['username'] ?? '-') ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-700"><?= number_format($row['total_trx'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">Rp <?= number_format($row['total_omset'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
    <?php elseif(!empty($reportType)): ?>
        <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
            Tidak ada data transaksi ditemukan untuk periode ini.
        </div>
    <?php endif; ?>

<?= $this->endSection() ?>