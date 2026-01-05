<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS4U</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row">
        
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            
            <div class="flex justify-center mb-4">
                <img src="<?= base_url('assets/images/POS4U.png') ?>" alt="Logo POS4U" class="w-48 h-auto object-contain">
            </div>

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Login</h2>
                <p class="text-sm text-gray-500">Point of sale for you</p>
            </div>

            <?php $session = session(); ?>
            <?php if ($session->getFlashdata('success')): ?>
                <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg mb-4 text-sm border border-green-200">
                    <?= $session->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if ($session->getFlashdata('error')): ?>
                <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm border border-red-200">
                    <?= $session->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/login') ?>" method="post" class="space-y-3">
                <?= csrf_field() ?>
                
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fa-solid fa-user text-gray-400"></i>
                    </span>
                    <input type="text" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-11 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all" 
                           id="username" name="username" placeholder="Username" required>
                </div>

                <div class="relative" x-data="{ show: false }">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </span>
                    <input :type="show ? 'text' : 'password'" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-11 pr-12 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all" 
                           id="password" name="password" placeholder="Password" required>
                    
                    <button type="button" @click="show = !show" 
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer transition-colors"
                            tabindex="-1">
                        <i class="fa-solid" :class="show ? 'fa-eye' : 'fa-eye-slash'"></i>
                    </button>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition duration-200 mt-4">
                    Login
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">Belum punya akun?
                    <a href="<?= base_url('/register') ?>" class="text-blue-600 hover:underline font-semibold">Register di sini</a>
                </p>
                <p class="mt-8 text-xs text-gray-400">Sale point for your shift.</p>
            </div>
        </div>

        <div class="hidden md:block w-1/2 relative overflow-hidden bg-gray-100">
            <img src="<?= base_url('assets/images/login.png') ?>" 
                 alt="Login Illustration" 
                 class="absolute inset-0 w-full h-full object-cover">
        </div>
    </div>
</body>
</html>