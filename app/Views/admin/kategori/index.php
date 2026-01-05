<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Manajemen Kategori') ?></h1>
        
        <button onclick="openModal('add')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer transition-all">
            <i class="fa-solid fa-plus -ml-1 mr-2 h-5 w-5"></i>
            Tambah Kategori
        </button>
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

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                                <th scope="col" class="w-12 px-6 py-3">
                                    <span class="sr-only">Expand</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Produk</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        
                        <tbody class="bg-white divide-y divide-gray-200" x-data="{ openKategoriId: null }">
                            <?php if (empty($kategori)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Belum ada data kategori.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($kategori as $kat): ?>
                                    <?php $currentId = $kat['id']; ?>
                                    
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $no++ ?></td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <?php if ($kat['total_produk'] > 0): ?>
                                                <button @click="openKategoriId = (openKategoriId === <?= $currentId ?> ? null : <?= $currentId ?>)" class="focus:outline-none">
                                                    <i class="fa-solid fa-chevron-down w-4 h-4 text-gray-400 transition-transform duration-200" 
                                                       :class="openKategoriId === <?= $currentId ?> && 'rotate-180'"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($kat['nama_kategori']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?= esc($kat['total_produk']) ?> Produk
                                                </span>
                                            </div> 
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button onclick="openModal('edit', <?= $currentId ?>, '<?= esc($kat['nama_kategori']) ?>')" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none transition-colors">
                                                <i class="fa-solid fa-pencil-alt h-4 w-4"></i>
                                            </button>

                                            <form action="<?= base_url('admin/kategori/' . $currentId) ?>" method="post" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? SEMUA PRODUK di dalamnya juga akan terhapus.');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none transition-colors">
                                                    <i class="fa-solid fa-trash-alt h-4 w-4"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <tr x-show="openKategoriId === <?= $currentId ?>" x-transition.opacity.duration.300ms>
                                        <td colspan="5" class="p-0">
                                            <div class="bg-gray-50 p-4 border-t border-b border-gray-200 shadow-inner">
                                                <?php if ($kat['total_produk'] > 0 && isset($produkByKategori[$currentId])): ?>
                                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 ml-1">Daftar Produk di Kategori Ini:</h4>
                                                    <table class="min-w-full bg-white rounded-md overflow-hidden border border-gray-200">
                                                        <thead class="bg-gray-100 border-b border-gray-200">
                                                            <tr>
                                                                <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                                                                <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                                                                <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                                                <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $noProduk = 1; foreach ($produkByKategori[$currentId] as $produk): ?>
                                                                <tr class="border-b border-gray-100 last:border-0 hover:bg-blue-50">
                                                                    <td class="py-2 px-4 text-sm text-gray-600"><?= $noProduk++ ?></td>
                                                                    <td class="py-2 px-4 text-sm font-medium text-gray-800"><?= esc($produk['nama_produk']) ?></td>
                                                                    <td class="py-2 px-4 text-sm text-gray-600">Rp <?= number_format($produk['harga'] ?? 0, 0, ',', '.') ?></td>
                                                                    <td class="py-2 px-4 text-sm text-gray-600">
                                                                        <span class="<?= ($produk['stok'] ?? 0) <= 5 ? 'text-red-600 font-bold' : 'text-gray-600' ?>">
                                                                            <?= esc($produk['stok'] ?? 0) ?> pcs
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modalKategori" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="relative z-10 inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200">
                
                <form id="formKategori" onsubmit="saveKategori(event)">
                    <?= csrf_field() ?>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div id="alert_umum" class="hidden mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline" id="msg_alert_umum"></span>
                        </div>

                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-list text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modalTitle">Tambah Kategori</h3>
                                <p class="text-sm text-gray-500 mb-4">Silakan masukkan data kategori dengan benar.</p>
                                
                                <div class="mt-4">
                                    <input type="hidden" id="id" name="id">
                                    <label for="nama_kategori" class="block text-sm font-semibold text-gray-700">Nama Kategori</label>
                                    <input type="text" name="nama_kategori" id="nama_kategori" 
                                           class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg p-2.5 border transition-all" 
                                           placeholder="Contoh: Makanan, Minuman, Elektronik..." required>
                                    <span id="error_nama_kategori" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" id="btnSave" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-base font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm transition-colors">
                            Simpan Data
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-semibold text-gray-700 hover:bg-gray-100 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalKategori');
        const form = document.getElementById('formKategori');
        const title = document.getElementById('modalTitle');
        const inputId = document.getElementById('id');
        const inputNama = document.getElementById('nama_kategori');
        const errorSpan = document.getElementById('error_nama_kategori');
        const btnSave = document.getElementById('btnSave');
        const alertUmum = document.getElementById('alert_umum');
        const msgAlertUmum = document.getElementById('msg_alert_umum');

        const BASE_URL = '<?= base_url('admin/kategori') ?>';

        function openModal(type, id = null, nama = '') {
            // Reset Error
            errorSpan.classList.add('hidden');
            alertUmum.classList.add('hidden');
            inputNama.classList.remove('border-red-500');
            
            if (type === 'add') {
                title.innerText = 'Tambah Kategori Baru';
                form.reset();
                inputId.value = '';
            } else {
                title.innerText = 'Edit Kategori';
                inputId.value = id;
                inputNama.value = nama;
            }
            
            modal.classList.remove('hidden');
            setTimeout(() => inputNama.focus(), 100);
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        async function saveKategori(e) {
            e.preventDefault();
            
            // Reset State
            btnSave.innerText = 'Menyimpan...';
            btnSave.disabled = true;
            errorSpan.classList.add('hidden');
            alertUmum.classList.add('hidden');
            inputNama.classList.remove('border-red-500');

            const id = inputId.value;
            const isUpdate = id !== '';
            const url = isUpdate ? `${BASE_URL}/update/${id}` : `${BASE_URL}/store`;

            const formData = new FormData(form);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Server Error: ${response.status}. Pastikan database sudah memiliki kolom 'deleted_at'.`);
                }

                const data = await response.json();

                if (data.status === 'success') {
                    closeModal();
                    
                    // --- UPDATE: Tambahkan SweetAlert disini ---
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                    
                } else {
                    // Reset Button jika error
                    btnSave.innerText = 'Simpan Data';
                    btnSave.disabled = false;

                    // Penanganan Error Validasi
                    if (data.errors) {
                        if (data.errors.nama_kategori) {
                            errorSpan.innerText = data.errors.nama_kategori;
                            errorSpan.classList.remove('hidden');
                            inputNama.classList.add('border-red-500');
                        }
                        if (data.errors.database) {
                            msgAlertUmum.innerText = data.errors.database;
                            alertUmum.classList.remove('hidden');
                        }
                    } else if (data.status === 'error') {
                        msgAlertUmum.innerText = data.message || 'Terjadi kesalahan sistem.';
                        alertUmum.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                // Reset Button jika error
                btnSave.innerText = 'Simpan Data';
                btnSave.disabled = false;
                
                msgAlertUmum.innerText = error.message || 'Gagal terhubung ke server.';
                alertUmum.classList.remove('hidden');
            }
        }
        
        document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
                closeModal();
            }
        };
    </script>
<?= $this->endSection() ?>