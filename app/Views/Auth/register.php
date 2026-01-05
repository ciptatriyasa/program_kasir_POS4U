<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - POS4U</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row">
        
        <div class="hidden md:flex w-1/2 relative overflow-hidden bg-white items-center justify-center p-4">
            <img src="<?= base_url('assets/images/register.png') ?>" 
                 alt="Register Illustration" 
                 class="w-full h-auto max-h-full object-contain">
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center">
            
            <div class="flex justify-center mb-4">
                <img src="<?= base_url('assets/images/POS4U.png') ?>" alt="Logo POS4U" class="w-44 h-auto object-contain">
            </div>

            <div class="text-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Registrasi Akun</h2>
                <p class="text-sm text-gray-500 mt-1">Daftarkan akun baru untuk menggunakan POS4U</p>
            </div>

            <?php $validation = \Config\Services::validation(); ?>
            <?php if ($validation->getErrors()): ?>
                <div class="bg-red-50 text-red-600 px-4 py-2 rounded-lg mb-4 text-xs border border-red-200">
                    <ul class="list-disc list-inside">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/register') ?>" method="post" 
                  x-data="{ 
                      password: '', 
                      confirm: '', 
                      show: false, 
                      match() { return this.password === this.confirm } 
                  }" 
                  class="space-y-3">
                
                <?= csrf_field() ?>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-id-card text-gray-400 text-sm"></i>
                    </span>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required placeholder="Nama Lengkap"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-user text-gray-400 text-sm"></i>
                    </span>
                    <input type="text" id="username" name="username" value="<?= old('username') ?>" required placeholder="Username"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400 text-sm"></i>
                    </span>
                    <input x-model="password" :type="show ? 'text' : 'password'" id="password" name="password" required placeholder="Password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pl-9 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                    
                    <button type="button" @click="show = !show" 
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer transition-colors"
                            tabindex="-1">
                        <i class="fa-solid" :class="show ? 'fa-eye' : 'fa-eye-slash'"></i>
                    </button>
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-circle-check text-gray-400 text-sm"></i>
                    </span>
                    <input x-model="confirm" :type="show ? 'text' : 'password'" id="pass_confirm" name="pass_confirm" required placeholder="Konfirmasi Password"
                        class="w-full border rounded-lg px-3 py-2.5 pl-9 pr-10 text-sm focus:outline-none transition-all"
                        :class="{
                            'border-red-500 ring-1 ring-red-500 focus:ring-red-500 focus:border-red-500': confirm && !match(), 
                            'border-green-500 ring-1 ring-green-500 focus:ring-green-500 focus:border-green-500': confirm && match(), 
                            'border-gray-300 focus:ring-blue-500 focus:border-blue-500': !confirm
                        }">
                    
                    <div class="absolute inset-y-0 right-0 px-3 flex items-center pointer-events-none" x-show="confirm">
                        <i x-show="match()" class="fa-solid fa-check text-green-500"></i>
                        <i x-show="!match()" class="fa-solid fa-times text-red-500"></i>
                    </div>
                </div>

                <p x-show="confirm && !match()" class="text-red-500 text-xs mt-1 ml-1" x-transition>
                    * Password tidak cocok!
                </p>

                <button type="submit" 
                        :disabled="!password || !confirm || !match()"
                        class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg shadow-md transition duration-200 mt-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-700 hover:shadow-lg">
                    Register
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">Sudah punya akun?
                    <a href="<?= base_url('/login') ?>" class="text-blue-600 hover:underline font-semibold">Login di sini</a>
                </p>
                <p class="mt-6 text-xs text-gray-400">Point of sale for your journey.</p>
            </div>
        </div>

    </div>
</body>
</html>