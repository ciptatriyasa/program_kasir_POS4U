<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Edit Profil') ?></h1>
        <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
            <p><?= session()->getFlashdata('success') ?></p>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            <p><?= session()->getFlashdata('error') ?></p>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('validation')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
            <p class="font-bold">Validasi Gagal:</p>
            <ul class="list-disc list-inside">
            <?php foreach (session()->getFlashdata('validation')->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="<?= base_url('/profile/update') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="px-4 py-5 sm:p-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-1 flex flex-col items-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                        <?php
                            $foto = $user['foto_profil'] ?? session()->get('foto_profil');
                            $avatarUrl = base_url('uploads/avatars/' . $foto);
                            if (empty($foto) || !file_exists(FCPATH . 'uploads/avatars/' . $foto)) {
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($user['nama_lengkap']) . "&background=random&size=128";
                            }
                        ?>
                        <img id="avatarPreview" class="h-32 w-32 rounded-full object-cover mb-4 shadow-sm border border-gray-200" src="<?= $avatarUrl ?>" alt="Foto Profil">
                        
                        <input type="file" name="foto_profil" id="foto_profil" class="hidden" 
                               onchange="document.getElementById('avatarPreview').src = window.URL.createObjectURL(this.files[0])">
                        
                        <label for="foto_profil" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fa-solid fa-camera -ml-1 mr-2 h-5 w-5 text-gray-400"></i>
                            Ganti Foto
                        </label>
                        <p class="text-xs text-gray-500 mt-2 text-center">PNG, JPG, JPEG (Max 2MB)</p>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" value="<?= esc($user['username']) ?>" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm" disabled>
                            <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah.</p>
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <input type="text" name="role" id="role" value="<?= esc(ucfirst($user['role'])) ?>" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm" disabled>
                        </div>

                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= old('nama_lengkap', $user['nama_lengkap']) ?>" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="<?= old('email', $user['email']) ?>" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                            <input type="text" name="no_hp" id="no_hp" value="<?= old('no_hp', $user['no_hp']) ?>" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"><?= esc(old('alamat', $user['alamat'])) ?></textarea>
                        </div>
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