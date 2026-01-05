<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - POS4U</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Tooltip Custom */
        .sidebar-tooltip {
            position: absolute;
            left: 100%; top: 50%; transform: translateY(-50%);
            margin-left: 12px; padding: 6px 12px;
            background-color: #1f2937; color: white;
            font-size: 12px; font-weight: 500; border-radius: 6px;
            white-space: nowrap; pointer-events: none; opacity: 0;
            transition: opacity 0.2s ease-in-out; z-index: 100;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .sidebar-tooltip::before {
            content: ""; position: absolute; top: 50%; right: 100%;
            margin-top: -5px; border-width: 5px; border-style: solid;
            border-color: transparent #1f2937 transparent transparent;
        }
        .group:hover .sidebar-tooltip { opacity: 1; }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900" 
      x-data="{ 
          mobileMenuOpen: false, 
          sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
          toggleSidebar() {
              this.sidebarOpen = !this.sidebarOpen;
              localStorage.setItem('sidebarOpen', this.sidebarOpen);
              
              // FIX: Trigger window resize event setelah animasi CSS (300ms) selesai
              // Ini memaksa Chart.js untuk menyesuaikan ukuran grafik secara otomatis saat sidebar berubah
              setTimeout(() => {
                  window.dispatchEvent(new Event('resize'));
              }, 310);
          }
      }"
      x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))">

    <?php
        $foto = session()->get('foto_profil');
        $avatarUrl = base_url('uploads/avatars/' . $foto);
        if (empty($foto) || !file_exists(FCPATH . 'uploads/avatars/' . $foto)) {
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode(session()->get('nama_lengkap')) . "&background=random";
        }
    ?>

    <div class="min-h-full flex">
        
        <div x-cloak
            :class="sidebarOpen ? 'md:w-64' : 'md:w-20'"
            class="hidden md:flex md:flex-col md:fixed md:inset-y-0 z-30 bg-white border-r border-gray-200 shadow-sm transition-all duration-300 ease-in-out relative"
            style="overflow: visible;">
            
            <button @click="toggleSidebar()" 
                    class="absolute -right-3 top-6 bg-white border border-gray-200 text-gray-500 hover:text-blue-600 p-1 rounded-full shadow-sm focus:outline-none z-50 flex items-center justify-center w-6 h-6 transition-colors group"
                    title="Toggle Sidebar">
                <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" 
                   :class="sidebarOpen ? 'rotate-180' : ''"></i>
            </button>

            <div class="h-16 flex items-center px-4 border-b border-gray-100 bg-white overflow-hidden" 
                 :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <div class="flex items-center whitespace-nowrap cursor-pointer overflow-hidden" @click="if(!sidebarOpen) toggleSidebar()">
                    <span class="text-3xl font-bold text-blue-600 flex-shrink-0"><i class="fa-solid fa-store"></i></span>
                    <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-3 text-xl font-bold text-gray-800 tracking-tight">POS4U</span>
                </div>
            </div>

            <div class="flex-1 flex flex-col pt-4 pb-4 px-3 space-y-1 overflow-visible">
                
                <?php 
                    // PERBAIKAN: Menambahkan 'kasir' dan 'kasir/dashboard' ke dalam logika aktif
                    $isActive = (url_is('dashboard') || url_is('admin/dashboard') || url_is('kasir') || url_is('kasir/dashboard')); 
                ?>
                <a href="<?= base_url('/dashboard') ?>"
                   class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <div class="w-8 flex justify-center flex-shrink-0">
                        <i class="fa-solid fa-home text-lg transition-colors <?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap overflow-hidden">Dashboard Utama</span>
                    <div x-show="!sidebarOpen" class="sidebar-tooltip">Dashboard Utama</div>
                </a>

                <?php if (session()->get('role') == 'admin'): ?>
                    
                    <?php $isActive = (strpos(current_url(), 'admin/users') !== false); ?>
                    <a href="<?= base_url('admin/users') ?>" class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                        <div class="w-8 flex justify-center flex-shrink-0">
                            <i class="fa-solid fa-users text-lg transition-colors <?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        </div>
                        <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap overflow-hidden">Manajemen User</span>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Manajemen User</div>
                    </a>

                    <?php $isActive = (strpos(current_url(), 'admin/stock-requests') !== false); ?>
                    <a href="<?= base_url('admin/stock-requests') ?>" class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                        <div class="w-8 flex justify-center flex-shrink-0 relative">
                            <i class="fa-solid fa-boxes-stacked text-lg transition-colors <?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                            <?php if (isset($pendingStockRequests) && $pendingStockRequests > 0): ?>
                                <span x-show="!sidebarOpen" class="absolute -top-1 -right-1 h-2.5 w-2.5 rounded-full bg-red-500 border border-white"></span>
                            <?php endif; ?>
                        </div>
                        <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap flex-1 overflow-hidden">Verifikasi Stok</span>
                        <?php if (isset($pendingStockRequests) && $pendingStockRequests > 0): ?>
                            <span x-show="sidebarOpen" class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $pendingStockRequests ?></span>
                        <?php endif; ?>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Verifikasi Stok</div>
                    </a>

                    <?php
                        $masterDataActive = (
                            strpos(current_url(), 'admin/kategori') !== false ||
                            strpos(current_url(), 'admin/suppliers') !== false || 
                            strpos(current_url(), 'admin/produk') !== false ||
                            strpos(current_url(), 'admin/penjualan') !== false ||
                            strpos(current_url(), 'admin/reports') !== false
                        );
                    ?>
                    <div x-data="{ open: <?= $masterDataActive ? 'true' : 'false' ?> }" class="relative group">
                        <button @click="if(!sidebarOpen) { toggleSidebar(); setTimeout(() => open = true, 150) } else { open = !open }"
                                class="w-full flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?= $masterDataActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <div class="w-8 flex justify-center flex-shrink-0">
                                <i class="fa-solid fa-database text-lg transition-colors <?= $masterDataActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                            </div>
                            <span x-show="sidebarOpen" x-transition.opacity class="ml-3 flex-1 text-left whitespace-nowrap overflow-hidden">Master Data</span>
                            <i x-show="sidebarOpen" class="fa-solid fa-chevron-right text-xs transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                        </button>
                        
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Master Data</div>

                        <div x-show="open && sidebarOpen" x-collapse class="space-y-1 mt-1 pl-11">
                            <a href="<?= base_url('admin/kategori') ?>" class="block px-3 py-2 text-sm rounded-md <?= strpos(current_url(), 'admin/kategori') ? 'text-blue-700 bg-blue-100 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Kategori</a>
                            <a href="<?= base_url('admin/suppliers') ?>" class="block px-3 py-2 text-sm rounded-md <?= strpos(current_url(), 'admin/suppliers') ? 'text-blue-700 bg-blue-100 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Supplier</a>
                            <a href="<?= base_url('admin/produk') ?>" class="block px-3 py-2 text-sm rounded-md <?= strpos(current_url(), 'admin/produk') ? 'text-blue-700 bg-blue-100 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Produk</a>
                            <a href="<?= base_url('admin/penjualan') ?>" class="block px-3 py-2 text-sm rounded-md <?= strpos(current_url(), 'admin/penjualan') ? 'text-blue-700 bg-blue-100 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Penjualan</a>
                            <a href="<?= base_url('admin/reports') ?>" class="block px-3 py-2 text-sm rounded-md <?= strpos(current_url(), 'admin/reports') ? 'text-blue-700 bg-blue-100 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Laporan</a>
                        </div>
                    </div>

                <?php elseif (session()->get('role') == 'kasir'): ?>
                    
                    <?php $isActive = (strpos(current_url(), 'kasir/pos') !== false); ?>
                    <a href="<?= base_url('kasir/pos') ?>" class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                        <div class="w-8 flex justify-center flex-shrink-0">
                            <i class="fa-solid fa-calculator text-lg transition-colors <?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        </div>
                        <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap overflow-hidden">Mesin Kasir</span>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Mesin Kasir</div>
                    </a>

                    <?php $isActive = (strpos(current_url(), 'kasir/stock') !== false); ?>
                    <a href="<?= base_url('kasir/stock') ?>" class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                        <div class="w-8 flex justify-center flex-shrink-0">
                            <i class="fa-solid fa-box-open text-lg transition-colors <?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        </div>
                        <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap overflow-hidden">Stok Barang</span>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Stok Barang</div>
                    </a>
                    
                    <?php $isActive = (strpos(current_url(), 'kasir/riwayat') !== false); ?>
                    <a href="<?= base_url('kasir/riwayat') ?>" class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg transition-all <?= $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                        <div class="w-8 flex justify-center flex-shrink-0">
                            <i class="fa-solid fa-clock-rotate-left text-lg transition-colors <?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        </div>
                        <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap overflow-hidden">Riwayat Transaksi</span>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip">Riwayat Transaksi</div>
                    </a>

                    <div class="my-4 border-t border-gray-100"></div>

                    <a href="<?= base_url('kasir/shift/close') ?>" class="group relative flex items-center px-2 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-all">
                        <div class="w-8 flex justify-center flex-shrink-0">
                            <i class="fa-solid fa-power-off text-lg group-hover:scale-110 transition-transform"></i>
                        </div>
                        <span x-show="sidebarOpen" x-transition.opacity class="ml-3 whitespace-nowrap font-bold overflow-hidden">Tutup Kasir</span>
                        <div x-show="!sidebarOpen" class="sidebar-tooltip !bg-red-700">Tutup Kasir</div>
                    </a>

                <?php endif; ?>
            </div>

            <div class="border-t border-gray-200 p-3 bg-white">
                <div x-data="{ open: false }" class="relative group">
                    <button @click="open = !open" class="flex items-center w-full focus:outline-none transition-colors rounded-lg p-2 hover:bg-gray-50">
                        <div class="relative flex-shrink-0">
                            <img class="h-9 w-9 rounded-full object-cover border border-gray-200 group-hover:border-blue-300" src="<?= $avatarUrl ?>" alt="Foto">
                            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
                        </div>
                        <div x-show="sidebarOpen" x-transition.opacity class="ml-3 text-left overflow-hidden">
                            <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate w-32"><?= esc(session()->get('nama_lengkap')) ?></p>
                            <p class="text-xs font-medium text-gray-500 group-hover:text-blue-600">Lihat Opsi</p>
                        </div>
                    </button>
                    <div x-show="!sidebarOpen" class="sidebar-tooltip top-0 transform-none mt-2"><?= esc(session()->get('nama_lengkap')) ?></div>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 bottom-full mb-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-50 ml-2">
                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-900">Signed in as</p>
                            <p class="text-sm font-medium text-gray-900 truncate"><?= esc(session()->get('nama_lengkap')) ?></p>
                        </div>
                        <div class="py-1">
                            <a href="<?= base_url('/profile') ?>" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50"><i class="fa-solid fa-user-circle mr-3 text-gray-400 group-hover:text-blue-500"></i> Profil</a>
                            <a href="<?= base_url('/logout') ?>" class="group flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50"><i class="fa-solid fa-sign-out-alt mr-3 text-red-400 group-hover:text-red-600"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div 
            :class="sidebarOpen ? 'md:pl-64' : 'md:pl-20'"
            class="flex flex-col flex-1 min-h-screen w-full max-w-full transition-all duration-300 ease-in-out bg-gray-100"
        >
            <div class="sticky top-0 z-10 md:hidden bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center p-4">
                    <span class="text-xl font-bold text-blue-600 flex items-center">
                        <i class="fa-solid fa-store mr-2"></i> POS4U
                    </span>
                    <button @click="mobileMenuOpen = true" class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fa-solid fa-bars h-6 w-6"></i>
                    </button>
                </div>
            </div>

            <main class="flex-1 py-6 px-4 sm:px-6 md:px-8 w-full max-w-full overflow-x-hidden">
                <?= $this->renderSection('content') ?>
            </main>
        </div>

        <div x-show="mobileMenuOpen" class="relative z-40 md:hidden" role="dialog" aria-modal="true">
            <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-gray-600 bg-opacity-75 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>

            <div class="fixed inset-0 flex z-40 pointer-events-none">
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                     class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-xl pointer-events-auto">
                    
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button @click="mobileMenuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <i class="fa-solid fa-xmark h-6 w-6 text-white"></i>
                        </button>
                    </div>

                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto bg-white">
                        <div class="flex-shrink-0 flex items-center px-4 mb-5">
                            <span class="text-2xl font-bold text-blue-600"><i class="fa-solid fa-store mr-2"></i> POS4U</span>
                        </div>
                        <nav class="px-2 space-y-1">
                            <a href="<?= base_url('/dashboard') ?>" class="group flex items-center px-3 py-3 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                <i class="fa-solid fa-home mr-4 text-lg text-gray-400"></i> Dashboard Utama
                            </a>
                            
                            <?php if (session()->get('role') == 'kasir'): ?>
                                <a href="<?= base_url('kasir/pos') ?>" class="group flex items-center px-3 py-3 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                    <i class="fa-solid fa-calculator mr-4 text-lg text-gray-400"></i> Mesin Kasir
                                </a>
                                <a href="<?= base_url('kasir/stock') ?>" class="group flex items-center px-3 py-3 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                    <i class="fa-solid fa-box-open mr-4 text-lg text-gray-400"></i> Stok Barang
                                </a>
                                <a href="<?= base_url('kasir/shift/close') ?>" class="group flex items-center px-3 py-3 text-base font-medium rounded-md text-red-600 hover:bg-red-50">
                                    <i class="fa-solid fa-power-off mr-4 text-lg text-red-500"></i> Tutup Kasir
                                </a>
                            <?php endif; ?>

                             <?php if (session()->get('role') == 'admin'): ?>
                                <a href="<?= base_url('admin/users') ?>" class="group flex items-center px-3 py-3 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                    <i class="fa-solid fa-users mr-4 text-lg text-gray-400"></i> Manajemen User
                                </a>
                                <a href="<?= base_url('admin/stock-requests') ?>" class="group flex items-center px-3 py-3 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                    <i class="fa-solid fa-boxes-stacked mr-4 text-lg text-gray-400"></i> Verifikasi Stok
                                </a>
                                <div class="mt-4 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>
                                <a href="<?= base_url('admin/kategori') ?>" class="group flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 ml-4"><i class="fa-solid fa-chevron-right mr-2 text-xs"></i> Kategori</a>
                                <a href="<?= base_url('admin/suppliers') ?>" class="group flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 ml-4"><i class="fa-solid fa-chevron-right mr-2 text-xs"></i> Supplier</a>
                                <a href="<?= base_url('admin/produk') ?>" class="group flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 ml-4"><i class="fa-solid fa-chevron-right mr-2 text-xs"></i> Produk</a>
                                <a href="<?= base_url('admin/penjualan') ?>" class="group flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 ml-4"><i class="fa-solid fa-chevron-right mr-2 text-xs"></i> Penjualan</a>
                                <a href="<?= base_url('admin/reports') ?>" class="group flex items-center px-3 py-2 text-base font-medium rounded-md text-gray-600 hover:bg-gray-50 ml-4"><i class="fa-solid fa-chevron-right mr-2 text-xs"></i> Laporan</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmLink(event, element, title = 'Lanjutkan?') {
            event.preventDefault(); const href = element.getAttribute('href');
            Swal.fire({ title: title, icon: 'question', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya' }).then((result) => { if (result.isConfirmed) window.location.href = href; });
        }
        function confirmAction(event, formElement, title = 'Yakin?', text = "", confirmBtnText = 'Ya') {
            event.preventDefault();
            Swal.fire({ title: title, text: text, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: confirmBtnText }).then((result) => { if (result.isConfirmed) formElement.submit(); });
        }
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= session()->getFlashdata('success') ?>', timer: 1500, showConfirmButton: false });
        <?php endif; ?>
    </script>
    
    <?= $this->renderSection('scripts') ?>
    
</body>
</html>