<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Edit User') ?></h1>
        <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Validasi Gagal:</p>
            <ul class="list-disc list-inside">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="px-4 py-5 sm:p-6">

                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Profil User</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" value="<?= esc($user['nama_lengkap']) ?>" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" value="<?= esc($user['username']) ?>" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="text" value="<?= esc($user['email'] ?? '-') ?>" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
                        <input type="text" value="<?= esc($user['no_hp'] ?? '-') ?>" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Admin</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="admin" <?= old('role', $user['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="kasir" <?= old('role', $user['role']) == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                        </select>
                    </div>

                    <div x-data="{ clearTimes: <?= ($user['jam_mulai'] === null && $user['jam_selesai'] === null) ? 'true' : 'false' ?> }">
                        <label class="block text-sm font-medium text-gray-700">Jam Kerja (Shift)</label>
                        <p class="text-xs text-gray-500 mb-2">Kosongkan jika kasir bisa login kapan saja.</p>
                        
                        <div class="grid grid-cols-2 gap-4" x-show="!clearTimes" x-transition>
                            <div>
                                <label for="jam_mulai" class="block text-xs font-medium text-gray-500">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" 
                                       value="<?= old('jam_mulai', $user['jam_mulai'] ? date('H:i', strtotime($user['jam_mulai'])) : '') ?>" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="jam_selesai" class="block text-xs font-medium text-gray-500">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" 
                                       value="<?= old('jam_selesai', $user['jam_selesai'] ? date('H:i', strtotime($user['jam_selesai'])) : '') ?>" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <button type="button" @click="clearTimes = !clearTimes; if(clearTimes) { document.getElementById('jam_mulai').value = ''; document.getElementById('jam_selesai').value = ''; }" 
                                class="mt-2 text-sm text-blue-600 hover:text-blue-800 focus:outline-none">
                            <span x-text="clearTimes ? 'Atur Jam Kerja Manual' : 'Kosongkan Jam Kerja (Login Kapan Saja)'"></span>
                        </button>
                        <input type="hidden" name="clear_shift_times" :value="clearTimes ? '1' : '0'">
                    </div>
                    
                    <div class="border-t border-gray-200 my-4"></div>

                    <p class="text-sm text-gray-500">Kosongkan password jika tidak ingin mengubahnya.</p>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="pass_confirm" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="pass_confirm" id="pass_confirm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>