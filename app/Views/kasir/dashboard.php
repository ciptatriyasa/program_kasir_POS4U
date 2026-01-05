<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-stretch">
    
    <div class="lg:col-span-2 bg-gradient-to-r from-purple-700 via-purple-600 to-blue-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden flex items-center justify-between min-h-[280px]">
        
        <div class="absolute top-0 right-0 w-72 h-72 bg-white opacity-5 rounded-full blur-3xl -mr-20 -mt-20 mix-blend-overlay"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-pink-500 opacity-20 rounded-full blur-3xl -ml-10 -mb-10 mix-blend-overlay"></div>

        <div class="relative z-10 flex flex-col justify-center h-full max-w-md">
            
            <h2 class="text-4xl font-extrabold mb-3 tracking-tight leading-tight">
                Halo, <br> <?= esc($user['nama_lengkap']) ?>! 👋
            </h2>
            <p class="text-lg text-purple-100 mb-8 opacity-90">
                Siap melayani pelanggan hari ini? Semangat bekerja!
            </p>

            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <?php
                        $foto = $user['foto_profil'];
                        $avatarUrl = base_url('uploads/avatars/' . $foto);
                        if (empty($foto) || !file_exists(FCPATH . 'uploads/avatars/' . $foto)) {
                            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($user['nama_lengkap']) . "&background=random&size=128";
                        }
                    ?>
                    <img src="<?= $avatarUrl ?>" alt="Profile" class="w-14 h-14 rounded-full border-2 border-white/30 shadow-sm object-cover">
                </div>

                 <div class="px-4 py-2 rounded-full backdrop-blur-md bg-white/10 flex items-center gap-2 border border-white/20">
                    <div class="w-2.5 h-2.5 rounded-full <?= $shift ? 'bg-green-400 animate-pulse shadow-[0_0_10px_rgba(74,222,128,0.5)]' : 'bg-red-400' ?>"></div>
                    <span class="font-semibold text-sm">
                        <?= $shift ? 'Shift Aktif' : 'Shift Tutup' ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="relative z-10 hidden md:block h-full w-2/5 pointer-events-none">
             <img src="https://cdn-icons-png.flaticon.com/512/4290/4290854.png" alt="Shopping Cart Illustration" class="absolute right-0 bottom-0 h-[100%] w-auto object-contain -mb-2 mr-4 opacity-90 drop-shadow-2xl">
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-4">Performa Hari Ini</h3>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Omset Pribadi</p>
                        <p class="font-bold text-gray-800 text-lg">Rp <?= number_format($todaySales, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Transaksi</p>
                        <p class="font-bold text-gray-800 text-lg"><?= $todayTrx ?> Struk</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex items-center justify-center bg-gray-50 rounded-lg p-2 border border-gray-100">
                 <i class="fa-regular fa-clock text-gray-400 mr-2"></i>
                 <span class="font-mono font-bold text-xl text-gray-700" id="liveClock"><?= date('H:i') ?></span>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <?php if ($shift): ?>
                <div class="bg-green-50 rounded-lg p-3 border border-green-100 mb-3">
                    <p class="text-xs text-green-700 mb-1">Shift dimulai:</p>
                    <p class="font-bold text-green-800"><?= date('H:i', strtotime($shift['opened_at'])) ?> WITA</p>
                    <p class="text-xs text-green-600 mt-1">Modal: Rp <?= number_format($shift['modal_awal'], 0, ',', '.') ?></p>
                </div>
                <a href="<?= base_url('kasir/shift/close') ?>" class="block w-full py-3 bg-red-50 text-red-600 text-center rounded-xl font-bold hover:bg-red-100 transition border border-red-100">
                    <i class="fa-solid fa-lock mr-2"></i> Tutup Shift
                </a>
            <?php else: ?>
                <p class="text-xs text-gray-400 mb-3 text-center">Anda belum bisa bertransaksi.</p>
                <a href="<?= base_url('kasir/shift/open') ?>" class="block w-full py-3 bg-blue-600 text-white text-center rounded-xl font-bold hover:bg-blue-700 shadow-md transition hover:shadow-lg">
                    <i class="fa-solid fa-key mr-2"></i> Buka Shift Baru
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<h3 class="text-lg font-bold text-gray-800 mb-4 px-1">Menu Utama</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <a href="<?= base_url('kasir/pos') ?>" class="group relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition-all duration-300 <?= !$shift ? 'opacity-60 cursor-not-allowed pointer-events-none grayscale' : '' ?>">
        <div class="absolute top-4 right-4 bg-blue-50 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </div>
        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 text-blue-600 group-hover:scale-110 transition-transform duration-300">
            <i class="fa-solid fa-calculator text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 text-lg">Mesin Kasir</h4>
        <p class="text-sm text-gray-500 mt-1">Mulai transaksi penjualan.</p>
    </a>

    <a href="<?= base_url('kasir/stock') ?>" class="group relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-200 transition-all duration-300">
        <div class="absolute top-4 right-4 bg-orange-50 text-orange-600 w-8 h-8 rounded-full flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition">
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </div>
        <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-4 text-orange-600 group-hover:scale-110 transition-transform duration-300">
            <i class="fa-solid fa-boxes-stacked text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 text-lg">Stok Barang</h4>
        <p class="text-sm text-gray-500 mt-1">Cek stok & request supplier.</p>
    </a>

    <a href="<?= base_url('kasir/riwayat') ?>" class="group relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition-all duration-300">
        <div class="absolute top-4 right-4 bg-purple-50 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition">
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </div>
        <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-4 text-purple-600 group-hover:scale-110 transition-transform duration-300">
            <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 text-lg">Riwayat</h4>
        <p class="text-sm text-gray-500 mt-1">Lihat riwayat transaksi.</p>
    </a>
    <a href="<?= base_url('profile') ?>" class="group relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all duration-300">
        <div class="absolute top-4 right-4 bg-gray-50 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center group-hover:bg-gray-600 group-hover:text-white transition">
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </div>
        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 text-gray-600 group-hover:scale-110 transition-transform duration-300">
            <i class="fa-solid fa-user-gear text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 text-lg">Profil Saya</h4>
        <p class="text-sm text-gray-500 mt-1">Ubah password & foto.</p>
    </a>
</div>

<h3 class="text-lg font-bold text-gray-800 mb-4 px-1 flex items-center">
    <i class="fa-solid fa-triangle-exclamation text-orange-500 mr-2"></i>
    Peringatan Stok Menipis
</h3>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <?php if (empty($lowStockProducts)): ?>
        <div class="p-8 text-center text-gray-500">
            <div class="bg-green-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-green-500">
                <i class="fa-solid fa-circle-check text-3xl"></i>
            </div>
            <p class="font-medium text-gray-600">Semua stok produk masih aman.</p>
            <p class="text-xs text-gray-400 mt-1">Tidak ada produk yang berada di bawah ambang batas stok.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4 text-center">Sisa Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($lowStockProducts as $p): ?>
                        <tr class="hover:bg-orange-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800"><?= esc($p['nama_produk']) ?></p>
                                <p class="text-[10px] text-gray-400">ID: #<?= $p['id'] ?></p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                    <i class="fa-solid fa-boxes-stacked mr-1.5 text-[10px]"></i>
                                    <?= $p['stok'] ?> Item
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= base_url('kasir/stock') ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    Minta Stok <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 p-3 text-center border-t border-gray-100">
            <p class="text-[10px] text-gray-400">Daftar ini menampilkan maksimal 5 produk yang paling krusial.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    setInterval(() => {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('liveClock').innerText = timeString;
    }, 1000);
</script>

<?= $this->endSection() ?>