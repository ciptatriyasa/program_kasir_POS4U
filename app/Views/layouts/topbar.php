<header class="bg-white shadow-sm border-b border-gray-200 z-30 sticky top-0">
    <div class="px-6 py-3 flex justify-between items-center">
        
        <div class="flex items-center gap-4">
            <?php if (!empty($backUrl)): ?>
                <a href="<?= $backUrl ?>" class="group inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 transition-colors duration-200" title="Kembali">
                    <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform duration-200"></i>
                </a>
            <?php else: ?>
                <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            <?php endif; ?>

            <h1 class="text-xl font-bold text-gray-800 tracking-tight">
                <?= esc($title ?? 'Dashboard') ?>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            
            <div class="hidden md:block relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                </span>
                <input type="text" 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64 bg-gray-50 focus:bg-white transition-colors" 
                       placeholder="Cari sesuatu...">
            </div>

            <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-regular fa-bell text-xl"></i>
                <span class="absolute top-1 right-2 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>

            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                A
            </div>
        </div>
    </div>
</header>