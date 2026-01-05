<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    
    <div class="w-full bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden mb-8">
        
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white opacity-10 rounded-full blur-3xl mix-blend-overlay"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-indigo-400 opacity-20 rounded-full blur-3xl mix-blend-overlay"></div>

        <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
            
            <div class="max-w-2xl">
                <div class="inline-flex items-center text-blue-200 font-medium text-sm mb-3 bg-white/10 px-3 py-1 rounded-full backdrop-blur-sm">
                    <i class="fa-solid fa-calendar-day mr-2"></i>
                    <?= date('d F Y') ?>
                </div>

                <h2 class="text-3xl sm:text-4xl font-extrabold mb-3 tracking-tight leading-tight">
                    Selamat Datang, <br> <span class="text-blue-200">Admin!</span> 👋
                </h2>
                <p class="text-base sm:text-lg text-blue-100 leading-relaxed opacity-90 mb-6">
                    Berikut adalah ringkasan performa toko Anda hari ini.
                </p>

                <a href="<?= base_url('admin/reports') ?>" class="inline-flex items-center bg-white text-blue-700 hover:bg-blue-50 px-6 py-3 rounded-xl font-bold text-sm transition shadow-lg group">
                    <i class="fa-solid fa-file-chart-column mr-2 group-hover:scale-110 transition-transform"></i>
                    Lihat Laporan Lengkap
                </a>
            </div>

            <div class="flex flex-col sm:flex-row flex-wrap gap-4 w-full xl:w-auto">
                
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl min-w-[200px] flex-1 shadow-lg hover:bg-white/15 transition">
                    <div class="flex items-center justify-between mb-3 gap-4">
                        <span class="text-blue-200 text-xs font-semibold uppercase tracking-wider">Penjualan Hari Ini</span>
                        <div class="bg-white/20 p-2 rounded-full text-white text-xs shadow-inner">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white tracking-wide">
                        Rp <?= number_format($todaySales ?? 0, 0, ',', '.') ?>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl min-w-[200px] flex-1 shadow-lg hover:bg-white/15 transition">
                     <div class="flex items-center justify-between mb-3 gap-4">
                        <span class="text-blue-200 text-xs font-semibold uppercase tracking-wider">Transaksi Hari Ini</span>
                        <div class="bg-white/20 p-2 rounded-full text-white text-xs shadow-inner">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-white tracking-wide">
                        <?= number_format($todayTransactions ?? 0, 0, ',', '.') ?>
                    </div>
                </div>

            </div>
        </div>
        
        <div class="absolute right-0 bottom-0 h-full w-1/3 pointer-events-none hidden 2xl:block">
             <img src="https://cdn-icons-png.flaticon.com/512/9930/9930349.png" alt="Dashboard Illustration" class="absolute right-10 bottom-0 h-[120%] w-auto object-contain -mb-6 opacity-10 mix-blend-overlay">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                <i class="fa-solid fa-wallet text-6xl text-green-600"></i>
            </div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Income (<?= date('Y') ?>)</p>
            <h3 class="text-3xl font-bold text-gray-800">Rp <?= number_format($totalIncome, 0, ',', '.') ?></h3>
            <div class="mt-3 text-xs text-green-600 flex items-center font-medium">
                <i class="fa-solid fa-arrow-trend-up mr-1.5"></i> Pendapatan Kotor
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                <i class="fa-solid fa-receipt text-6xl text-orange-600"></i>
            </div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Transaksi</p>
            <h3 class="text-3xl font-bold text-gray-800"><?= number_format($totalTransaksi, 0, ',', '.') ?></h3>
            <div class="mt-3 text-xs text-orange-600 flex items-center font-medium">
                <i class="fa-solid fa-check-circle mr-1.5"></i> Transaksi Berhasil
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 text-white shadow-lg flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-20 group-hover:opacity-30 transition">
                <i class="fa-solid fa-boxes-stacked text-6xl text-white"></i>
            </div>
            
            <div>
                <div class="flex items-center text-yellow-400 gap-2 mb-2">
                     <i class="fa-solid fa-bell animate-pulse"></i>
                     <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Notifikasi</span>
                </div>
                <h3 class="text-4xl font-bold mt-1 relative z-10"><?= $pendingStockRequests ?></h3>
            </div>

            <div class="flex items-end justify-between mt-4 relative z-10">
                <p class="text-sm text-gray-400 font-medium pb-1">Permintaan Stok Baru</p>
                <a href="<?= base_url('admin/stock-requests') ?>" class="text-xs bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition backdrop-blur-sm border border-white/10 shadow-sm flex items-center">
                    Cek Request <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-indigo-50 p-4 rounded-xl text-indigo-600">
                <i class="fa-solid fa-box-open text-2xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800"><?= number_format($totalItemTerjual, 0, ',', '.') ?></h3>
                <p class="text-sm text-gray-500 font-medium">Item Terjual (YTD)</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-green-50 p-4 rounded-xl text-green-600">
                <i class="fa-solid fa-user-clock text-2xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800"><?= number_format($kasirAktif, 0, ',', '.') ?></h3>
                <p class="text-sm text-gray-500 font-medium">Kasir Aktif</p>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-pink-50 p-4 rounded-xl text-pink-600">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800"><?= number_format($totalUserAktif, 0, ',', '.') ?></h3>
                <p class="text-sm text-gray-500 font-medium">Total User Terdaftar</p>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col w-full overflow-hidden">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="p-2 bg-red-50 text-red-500 rounded-lg">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    Stok Menipis
                </h3>
                <a href="<?= base_url('admin/produk') ?>" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition flex items-center">
                    Lihat Semua <i class="fa-solid fa-chevron-right text-xs ml-1"></i>
                </a>
            </div>
            
            <div class="overflow-x-auto flex-1 w-full">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 rounded-l-lg w-10 text-center">No</th>
                            <th class="py-3 px-4">Produk</th>
                            <th class="py-3 px-4 text-center rounded-r-lg">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(!empty($stokMenipis)) : ?>
                            <?php foreach($stokMenipis as $index => $item) : ?>
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="py-3 px-4 text-center text-gray-400 font-medium"><?= $index + 1 ?></td>
                                    <td class="py-3 px-4 font-medium text-gray-700 group-hover:text-blue-600 transition"><?= esc($item['nama_produk']) ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                            <?= esc($item['stok']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-400 italic">
                                    <i class="fa-solid fa-check-circle text-green-400 text-2xl mb-2 block"></i> Stok aman!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col w-full overflow-hidden">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <div class="p-2 bg-yellow-50 text-yellow-500 rounded-lg">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    Terlaris Bulan Ini
                </h3>
                <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-2 py-1 rounded-md border border-gray-200">
                    <?= date('F Y') ?>
                </span>
            </div>

            <div class="space-y-4 flex-1 w-full">
                <?php if(!empty($produkTerlaris)) : ?>
                    <?php $maxVal = $produkTerlaris[0]['total_terjual'] ?? 1; ?>
                    <?php foreach($produkTerlaris as $index => $item) : ?>
                        <div class="flex items-center gap-3 group">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm shadow-sm
                                <?= $index == 0 ? 'bg-gradient-to-br from-yellow-300 to-yellow-500 text-white' : 
                                   ($index == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400 text-white' : 
                                   ($index == 2 ? 'bg-gradient-to-br from-orange-300 to-orange-400 text-white' : 'bg-gray-100 text-gray-400')) ?>">
                                <?= $index + 1 ?>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-blue-600 transition"><?= esc($item['nama_produk']) ?></p>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                    <?php $percent = ($item['total_terjual'] / $maxVal) * 100; ?>
                                    <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: <?= $percent ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0 text-right">
                                <span class="block text-sm font-bold text-gray-800"><?= esc($item['total_terjual']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="py-8 text-center text-gray-400 italic">Belum ada penjualan bulan ini.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 w-full overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Analisis Penjualan Bulanan</h3>
                <p class="text-sm text-gray-500">Overview performa penjualan</p>
            </div>
            <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full border border-blue-100">
                <i class="fa-regular fa-calendar mr-1"></i> Tahun Ini
            </span>
        </div>
        
        <div class="relative h-80 mb-8 w-full">
            <canvas id="monthlySalesChart"></canvas>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
            <div class="border border-gray-100 rounded-xl p-5 shadow-sm bg-gray-50/50 w-full overflow-hidden">
                <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Performa 7 Hari Terakhir</h4>
                <div class="relative h-48 w-full">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>

            <div class="border border-gray-100 rounded-xl p-5 shadow-sm bg-gray-50/50 w-full overflow-hidden">
                <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Komparasi Tahunan</h4>
                <div class="relative h-48 w-full">
                    <canvas id="annualSalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // FORMATTER RUPIAH
    const axisFormatter = (value) => {
        if (value === 0) return '0';
        if (Math.abs(value) >= 1000000) return (value / 1000000).toFixed(1).replace(/\.0$/, '') + ' Jt';
        if (Math.abs(value) >= 1000) return (value / 1000).toFixed(1).replace(/\.0$/, '') + ' Rb';
        return value;
    };
    const rupiahFormatter = (value) => {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
    };

    // 1. GRAFIK BULANAN (Area)
    const ctxMonthly = document.getElementById('monthlySalesChart').getContext('2d');
    let gradient = ctxMonthly.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); 
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlySalesLabels) ?>,
            datasets: [{
                label: 'Penjualan',
                data: <?= json_encode($monthlySalesData) ?>,
                backgroundColor: gradient, borderColor: '#4f46e5', borderWidth: 3,
                pointBackgroundColor: '#ffffff', pointBorderColor: '#4f46e5',
                pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.4 
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8, displayColors: false,
                    callbacks: { label: (c) => `Total: Rp ${rupiahFormatter(c.raw)}` }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f3f4f6' }, ticks: { callback: axisFormatter, font: {size: 11} } },
                x: { grid: { display: false }, ticks: { font: {size: 11} } }
            }
        }
    });

    // 2. GRAFIK HARIAN (Bar)
    new Chart(document.getElementById('dailySalesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($dailySalesLabels) ?>,
            datasets: [{
                label: 'Harian',
                data: <?= json_encode($dailySalesData) ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.8)', borderColor: 'rgb(16, 185, 129)', borderWidth: 1, borderRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => `Rp ${rupiahFormatter(c.raw)}` } } },
            scales: { y: { beginAtZero: true, ticks: { callback: axisFormatter, font: {size: 10} } }, x: { grid: { display: false }, ticks: { font: {size: 10} } } }
        }
    });

    // 3. GRAFIK TAHUNAN (Line Komparasi)
    new Chart(document.getElementById('annualSalesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlySalesLabels) ?>,
            datasets: [
                { label: '<?= $currentYear ?>', data: <?= json_encode($annualDataCurrent) ?>, borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)', borderWidth: 2, tension: 0.3, pointRadius: 2 },
                { label: '<?= $lastYear ?>', data: <?= json_encode($annualDataLast) ?>, borderColor: '#9ca3af', backgroundColor: 'rgba(156, 163, 175, 0.1)', borderWidth: 2, borderDash: [5, 5], tension: 0.3, pointRadius: 2 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: {size: 10}, usePointStyle: true } }, tooltip: { callbacks: { label: (c) => `${c.dataset.label}: Rp ${rupiahFormatter(c.raw)}` } } },
            scales: { y: { beginAtZero: true, ticks: { callback: axisFormatter, font: {size: 9} } }, x: { grid: { display: false }, ticks: { font: {size: 9} } } }
        }
    });
});
</script>
<?= $this->endSection() ?>