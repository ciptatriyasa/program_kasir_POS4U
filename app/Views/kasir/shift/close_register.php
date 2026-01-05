<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto mt-6">
    
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Laporan Akhir Shift</h1>
    <p class="text-gray-500 mb-6">Pastikan data fisik sesuai dengan data sistem sebelum menutup kasir.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Ringkasan Penjualan Saya</h3>
            
            <div class="flex justify-between text-sm mb-4 bg-gray-50 p-3 rounded">
                <div>
                    <span class="block text-gray-500">Mulai Shift</span>
                    <span class="font-bold"><?= date('H:i', strtotime($shift['opened_at'])) ?></span>
                </div>
                <div class="text-right">
                    <span class="block text-gray-500">Durasi</span>
                    <?php 
                        $start = new DateTime($shift['opened_at']);
                        $now = new DateTime();
                        $diff = $start->diff($now);
                        echo "<span class='font-bold'>" . $diff->h . " Jam " . $diff->i . " Menit</span>";
                    ?>
                </div>
            </div>

            <table class="w-full text-sm mb-4">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="py-2 px-2 text-left">Metode</th>
                        <th class="py-2 px-2 text-center">Trx</th>
                        <th class="py-2 px-2 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($summary as $row): ?>
                    <tr>
                        <td class="py-2 px-2 capitalize"><?= esc($row['metode_pembayaran']) ?></td>
                        <td class="py-2 px-2 text-center"><?= esc($row['qty']) ?></td>
                        <td class="py-2 px-2 text-right font-medium">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($summary)): ?>
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Belum ada transaksi</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-t-2 border-gray-200">
                    <tr>
                        <td class="py-2 px-2 font-bold">TOTAL OMSET</td>
                        <td class="py-2 px-2 text-center font-bold"><?= $totalTransactions ?></td>
                        <td class="py-2 px-2 text-right font-bold text-blue-600 text-lg">Rp <?= number_format($totalCashSales + $totalNonCash, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm text-yellow-800 font-semibold flex justify-between">
                    <span>Non-Tunai (QRIS/Bank):</span>
                    <span>Rp <?= number_format($totalNonCash, 0, ',', '.') ?></span>
                </p>
                <p class="text-xs text-yellow-600 mt-1">*Uang ini masuk langsung ke rekening, tidak perlu dihitung di laci.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-green-600 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Setoran & Tutup Kasir</h3>
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Modal Awal</span>
                        <span class="font-medium">Rp <?= number_format($shift['modal_awal'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Penjualan Tunai (Cash)</span>
                        <span class="font-medium text-green-600">+ Rp <?= number_format($totalCashSales, 0, ',', '.') ?></span>
                    </div>
                    <div class="border-t my-2"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Target Uang di Laci</span>
                        <span class="text-xl font-bold text-green-700">Rp <?= number_format($expectedCash, 0, ',', '.') ?></span>
                    </div>
                </div>

                <form action="<?= base_url('kasir/shift/close') ?>" method="post" x-data="{ fisik: 0, target: <?= $expectedCash ?> }">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hitung Total Uang Fisik</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="uang_fisik" x-model.number="fisik" required min="0" 
                                   class="focus:ring-green-500 focus:border-green-500 block w-full pl-12 pr-4 py-3 sm:text-lg border-gray-300 rounded-md" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-md text-center transition-colors duration-200"
                         :class="{
                             'bg-green-100 text-green-800 border border-green-200': (fisik - target) == 0,
                             'bg-red-100 text-red-800 border border-red-200': (fisik - target) != 0
                         }">
                        <p class="text-xs font-bold uppercase tracking-wider mb-1">Status Balance</p>
                        <p class="text-lg font-bold">
                            <span x-show="(fisik - target) == 0">PAS / BALANCE <i class="fa-solid fa-check-circle ml-1"></i></span>
                            <span x-show="(fisik - target) < 0">KURANG: Rp <span x-text="Math.abs(fisik - target).toLocaleString('id-ID')"></span></span>
                            <span x-show="(fisik - target) > 0">LEBIH: Rp <span x-text="(fisik - target).toLocaleString('id-ID')"></span></span>
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="2" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Keterangan jika ada selisih..."></textarea>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            onclick="return confirm('Apakah Anda yakin data uang fisik sudah benar? Shift akan ditutup.')">
                        <i class="fa-solid fa-lock mr-2 mt-0.5"></i> Simpan & Tutup Shift
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>