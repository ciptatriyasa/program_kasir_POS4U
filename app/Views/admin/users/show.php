<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Detail User') ?></h1>
        <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center">

                <!-- 
                    PERUBAHAN DI SINI:
                    Menggunakan style="..." untuk memaksa ukuran 80px x 80px.
                -->
                <div class="flex-shrink-0 overflow-hidden rounded-full border border-gray-200" 
                     style="width: 80px; height: 80px;"> 
                    <?php
                        $foto = $user['foto_profil'];
                        $avatarUrl = base_url('uploads/avatars/' . $foto);
                        if (empty($foto) || !file_exists(FCPATH . 'uploads/avatars/' . $foto)) {
                            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($user['nama_lengkap']) . "&background=random&size=128";
                        }
                    ?>
                    <!-- 
                        PERUBAHAN DI SINI:
                        Menambahkan style="..." untuk memastikan gambar mengisi container-nya.
                    -->
                    <img class="object-cover" 
                         style="width: 100%; height: 100%; object-fit: cover;" 
                         src="<?= $avatarUrl ?>" alt="Foto <?= esc($user['nama_lengkap']) ?>"> 
                </div>

                <!-- (Bagian Teks) -->
                <div class="ml-4 flex-grow"> 
                    <h3 class="text-xl leading-6 font-medium text-gray-900">
                        <?= esc($user['nama_lengkap']) ?>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        @<?= esc($user['username']) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Detail Info (Tidak ada perubahan di bawah sini) -->
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Kontak</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div><?= esc($user['email'] ?? '-') ?></div>
                        <div class="text-gray-600"><?= esc($user['no_hp'] ?? '-') ?></div>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($user['alamat'] ?? '-') ?>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                         <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full <?= $user['role'] == 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                            <?= esc(ucfirst($user['role'])) ?>
                        </span>
                    </dd>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Shift Kerja</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc($nama_shift) ?>
                        <?php if ($user['jam_mulai']): ?>
                            <span class="text-gray-500">(<?= esc(date('H:i', strtotime($user['jam_mulai']))) ?> - <?= esc(date('H:i', strtotime($user['jam_selesai']))) ?>)</span>
                        <?php endif; ?>
                    </dd>
                </div>

                 <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500">Terdaftar Sejak</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= esc(date('d M Y, H:i', strtotime($user['created_at']))) ?>
                    </dd>
                </div>

            </dl>
        </div>
    </div>
<?= $this->endSection() ?>