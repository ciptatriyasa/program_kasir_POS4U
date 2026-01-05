<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Manajemen Supplier</h1>
    <button onclick="openModal('add')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-all">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Supplier
    </button>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= session()->getFlashdata('success') ?>',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= session()->getFlashdata('error') ?>',
        });
    </script>
<?php endif; ?>

<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-10 border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama & Alamat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $no = 1; foreach ($suppliers as $s): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $no++ ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900"><?= esc($s['nama_supplier']) ?></div>
                    <div class="text-xs text-gray-500 truncate max-w-xs"><?= esc($s['alamat']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        <?= esc($s['nama_kategori'] ?? 'Umum') ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center"><i class="fa-solid fa-phone text-[10px] mr-1 text-gray-400"></i> <?= esc($s['no_telp']) ?></div>
                    <div class="flex items-center"><i class="fa-solid fa-envelope text-[10px] mr-1 text-gray-400"></i> <?= esc($s['email']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end items-center gap-2">
                        <a href="<?= base_url('admin/suppliers/catalog/' . $s['id']) ?>" class="mr-2 text-gray-400 hover:text-blue-600 transition-colors" title="Lihat Katalog">
                            <i class="fa-solid fa-list"></i>
                        </a>

                        <button onclick="openModal('detail', <?= $s['id'] ?>, '<?= esc($s['nama_supplier']) ?>', '<?= $s['id_kategori'] ?>', '<?= esc($s['alamat']) ?>', '<?= esc($s['no_telp']) ?>', '<?= esc($s['email']) ?>')" 
                            class="w-8 h-8 inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-sm transition-all" title="Detail">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        <button onclick="openModal('edit', <?= $s['id'] ?>, '<?= esc($s['nama_supplier']) ?>', '<?= $s['id_kategori'] ?>', '<?= esc($s['alamat']) ?>', '<?= esc($s['no_telp']) ?>', '<?= esc($s['email']) ?>')" 
                            class="w-8 h-8 inline-flex items-center justify-center bg-yellow-500 hover:bg-yellow-600 text-white rounded-md shadow-sm transition-all" title="Edit">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        
                        <form id="form-delete-<?= $s['id'] ?>" action="<?= base_url('admin/suppliers/' . $s['id']) ?>" method="post" class="inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" onclick="confirmDelete(<?= $s['id'] ?>, '<?= esc($s['nama_supplier']) ?>')" 
                                class="w-8 h-8 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded-md shadow-sm transition-all" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="border-t-4 border-blue-500 pt-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-truck-ramp-box mr-2 text-blue-600"></i>
                Riwayat Barang Keluar Supplier
            </h2>
            <p class="text-sm text-gray-500">
                <?php if(!empty($filterDate)): ?>
                    Menampilkan data tanggal: <span class="font-bold text-blue-600"><?= date('d M Y', strtotime($filterDate)) ?></span>
                <?php elseif(!empty($filterMonth)): ?>
                    Menampilkan data bulan: <span class="font-bold text-blue-600"><?= date('F Y', strtotime($filterMonth)) ?></span>
                <?php else: ?>
                    Data barang masuk ke toko pada hari ini (<?= date('d M Y') ?>).
                <?php endif; ?>
            </p>
        </div>

        <form action="<?= base_url('admin/suppliers') ?>" method="get" class="flex flex-wrap items-end gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200 shadow-sm w-full lg:w-auto">
            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Supplier</label>
                <select name="filter_supplier" class="block w-full md:w-44 pl-3 pr-10 py-2 text-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md">
                    <option value="">-- Semua Supplier --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= ($filterSupplier == $sup['id']) ? 'selected' : '' ?>><?= esc($sup['nama_supplier']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Pilih Hari</label>
                <input type="date" name="filter_date" value="<?= $filterDate ?? '' ?>" class="block w-full md:w-40 pl-3 pr-3 py-2 text-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm">
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Pilih Bulan</label>
                <input type="month" name="filter_month" value="<?= $filterMonth ?? '' ?>" class="block w-full md:w-44 pl-3 pr-3 py-2 text-sm border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <?php if(!empty($filterSupplier) || !empty($filterDate) || !empty($filterMonth)): ?>
                    <a href="<?= base_url('admin/suppliers') ?>" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-red-600 hover:bg-red-50" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th> 
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Keluar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th> 
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                            <i class="fa-solid fa-box-open text-gray-300 text-3xl mb-2 block"></i>
                            Tidak ada data barang keluar untuk filter ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($history as $h): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $no++ ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d M Y', strtotime($h['updated_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900"><?= esc($h['nama_supplier']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <?= esc($h['nama_kategori'] ?? 'Umum') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?= esc($h['nama_produk']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> <?= esc($h['jumlah_diminta']) ?> Pcs
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('H:i', strtotime($h['updated_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalSupplier" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="relative z-10 inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200">
            <form id="formSupplier" onsubmit="saveSupplier(event)">
                <?= csrf_field() ?>
                <input type="hidden" id="id" name="id">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-truck text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modalTitle">Tambah Supplier</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Nama Supplier</label>
                                    <input type="text" name="nama_supplier" id="nama_supplier" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg p-2 border" required>
                                    <span id="error_nama_supplier" class="text-red-500 text-xs hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                                    <select name="id_kategori" id="id_kategori" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm sm:text-sm">
                                        <?php foreach ($kategori as $kat): ?>
                                            <option value="<?= $kat['id'] ?>"><?= esc($kat['nama_kategori']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">No. Telp</label>
                                    <input type="text" name="no_telp" id="no_telp" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm sm:text-sm">
                                    <span id="error_email" class="text-red-500 text-xs hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Alamat</label>
                                    <textarea name="alamat" id="alamat" rows="2" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" id="btnSave" class="w-full inline-flex justify-center rounded-lg px-5 py-2.5 bg-blue-600 text-white font-semibold hover:bg-blue-700 sm:w-auto sm:text-sm transition-all shadow-md">Simpan</button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-lg px-5 py-2.5 bg-white text-gray-700 border border-gray-300 font-semibold hover:bg-gray-100 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modalSupplier');
    const form = document.getElementById('formSupplier');
    const inputId = document.getElementById('id');
    const btnSave = document.getElementById('btnSave');
    const BASE_URL = '<?= base_url('admin/suppliers') ?>';

    // FUNGSI UTAMA: Menangani modal (Add, Edit, dan Detail)
    function openModal(type, id = null, nama = '', kategoriId = '', alamat = '', telp = '', email = '') {
        // Reset pesan error
        document.querySelectorAll('[id^="error_"]').forEach(el => el.classList.add('hidden'));
        
        // Ambil semua elemen input di form
        const inputs = form.querySelectorAll('input, select, textarea');
        
        // Reset kondisi awal (enable semua input & tampilkan tombol simpan)
        inputs.forEach(input => input.disabled = false);
        btnSave.classList.remove('hidden');

        if (type === 'add') {
            document.getElementById('modalTitle').innerText = 'Tambah Supplier';
            form.reset();
            inputId.value = '';
        } else if (type === 'edit') {
            document.getElementById('modalTitle').innerText = 'Edit Supplier';
            fillForm(id, nama, kategoriId, alamat, telp, email);
        } else if (type === 'detail') {
            // MODE DETAIL: Ubah judul, isi form, lalu kunci (disable) semua input
            document.getElementById('modalTitle').innerText = 'Detail Supplier';
            fillForm(id, nama, kategoriId, alamat, telp, email);
            
            // Disable semua input
            inputs.forEach(input => input.disabled = true);
            // Sembunyikan tombol simpan
            btnSave.classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
    }

    // Helper untuk mengisi form
    function fillForm(id, nama, kategoriId, alamat, telp, email) {
        inputId.value = id;
        document.getElementById('nama_supplier').value = nama;
        document.getElementById('id_kategori').value = kategoriId;
        document.getElementById('alamat').value = alamat;
        document.getElementById('no_telp').value = telp;
        document.getElementById('email').value = email;
    }

    function closeModal() { modal.classList.add('hidden'); }

    // --- FUNGSI BARU: Konfirmasi Delete dengan SweetAlert ---
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Supplier?',
            text: `Anda yakin ingin menghapus data supplier "${nama}"? Data yang dihapus tidak dapat dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form secara programmatically jika user klik 'Ya'
                document.getElementById('form-delete-' + id).submit();
            }
        });
    }

    // --- FUNGSI UPDATE: Simpan dengan Notifikasi AJAX ---
    async function saveSupplier(e) {
        e.preventDefault();
        btnSave.innerText = 'Menyimpan...';
        btnSave.disabled = true;
        
        const id = inputId.value;
        const url = id ? `${BASE_URL}/update/${id}` : `${BASE_URL}/create`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.status === 'success') {
                // [FIX] Tutup Modal TERLEBIH DAHULU sebelum menampilkan Alert
                closeModal(); 

                // Beri jeda sangat singkat agar animasi tutup modal selesai
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                }, 300); // 300ms delay
                
            } else if (data.errors) {
                // Handle Error Validasi (tetap menampilkan teks merah di form)
                Object.keys(data.errors).forEach(key => {
                    const errEl = document.getElementById(`error_${key}`);
                    if (errEl) { errEl.innerText = data.errors[key]; errEl.classList.remove('hidden'); }
                });
                
                // Opsional: Tampilkan toast kecil error
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                });
                Toast.fire({ icon: 'error', title: 'Periksa kembali inputan Anda.' });
            }
        } catch (error) { 
            Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
        } finally { 
            btnSave.innerText = 'Simpan'; 
            btnSave.disabled = false; 
        }
    }
</script>
<?= $this->endSection() ?>