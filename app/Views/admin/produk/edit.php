<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Edit Produk') ?></h1>
        <a href="<?= base_url('admin/produk') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5"></i>
            Kembali
        </a>
    </div>

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
        <form action="<?= base_url('admin/produk/update/' . $produk['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 border-b pb-2">Informasi Produk</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="nama_produk" id="nama_produk"
                                   value="<?= old('nama_produk', $produk['nama_produk']) ?>" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="id_kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select id="id_kategori" name="id_kategori" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="" disabled>Pilih Kategori</option>
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat['id'] ?>" <?= (old('id_kategori', $produk['id_kategori']) == $kat['id']) ? 'selected' : '' ?>>
                                        <?= esc($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="stok" class="block text-sm font-medium text-gray-700">Stok (Read Only)</label>
                            <input type="number" name="stok" id="stok"
                                   value="<?= old('stok', $produk['stok']) ?>" readonly
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                            <p class="mt-1 text-xs text-red-500">
                                <i class="fa-solid fa-lock mr-1"></i>
                                Stok hanya bisa bertambah melalui permintaan stok Kasir.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 border-b pb-2">Harga & Lokasi</h3>
                    <div class="space-y-4">
                        
                        <div>
                            <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli (Modal)</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">Rp</span>
                                <input type="number" name="harga_beli" id="harga_beli"
                                       value="<?= old('harga_beli', $produk['harga_beli'] ?? 0) ?>" required min="0"
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300">
                            </div>
                        </div>

                        <div>
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">Rp</span>
                                <input type="number" name="harga" id="harga"
                                       value="<?= old('harga', $produk['harga']) ?>" required min="0"
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300">
                            </div>
                        </div>
                        
                        <div>
                            <label for="exp_date" class="block text-sm font-medium text-gray-700">Tanggal Kedaluwarsa (Exp Date)</label>
                            <input type="date" name="exp_date" id="exp_date" 
                                   value="<?= old('exp_date', $produk['exp_date'] ?? '') ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="posisi_rak" class="block text-sm font-medium text-gray-700">Posisi Rak</label>
                            <input type="text" name="posisi_rak" id="posisi_rak" 
                                   value="<?= old('posisi_rak', $produk['posisi_rak'] ?? '') ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: A3-B">
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:p-6 border-t border-gray-200 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 border-b pb-2">Foto Produk</h3>
                    <?php 
                        // Jika $fotoUrl kosong (produk lama yang fotonya hilang/invalid), gunakan placeholder
                        $defaultFoto = base_url('assets/images/placeholder_product.png'); 
                        $currentFoto = $fotoUrl ?? $defaultFoto;
                    ?>
                    <div class="mt-1 flex items-center mb-2">
                        <img id="fotoPreview" class="h-24 w-24 object-cover rounded-lg border border-gray-300 shadow-sm mr-4" 
                             src="<?= esc($currentFoto) ?>" alt="Preview"
                             onerror="this.onerror=null;this.src='<?= esc($defaultFoto) ?>';">
                        
                        <label class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fa-solid fa-cloud-upload-alt -ml-1 mr-2 h-5 w-5"></i>
                            Ganti Foto
                            <input type="file" name="foto_produk" id="foto_produk" class="sr-only" 
                                   onchange="document.getElementById('fotoPreview').src = window.URL.createObjectURL(this.files[0])">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG/JPEG (Max 2MB). Biarkan kosong untuk foto lama.</p>
                    <?php if (!empty($produk['foto_produk'])): ?>
                        <input type="hidden" name="old_foto_produk" value="<?= esc($produk['foto_produk']) ?>">
                    <?php endif; ?>
                </div>

                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 border-b pb-2">Barcode Produk</h3>
                    <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode Nomor</label>
                    <input type="text" name="barcode" id="barcode"
                           value="<?= old('barcode', $produk['barcode']) ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menghapus barcode.</p>

                    <?php if (!empty($barcodeImage)): ?>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Barcode</label>
                        <div class="p-4 bg-white border border-gray-300 rounded-md inline-block shadow-sm">
                            <img src="<?= $barcodeImage ?>" alt="Barcode <?= esc($produk['barcode']) ?>">
                            <p class="text-center text-sm font-mono tracking-widest mt-2"><?= esc($produk['barcode']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>