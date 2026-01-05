<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Manajemen Produk') ?></h1>
        
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/produk/new') ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fa-solid fa-plus -ml-1 mr-2 h-5 w-5"></i>
                Tambah Produk
            </a>
            
            <a href="<?= base_url('admin/produk/download-barcodes') ?>" 
                class="inline-flex items-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100"
                onclick="confirmLink(event, this, 'Download semua barcode?')">
                    <i class="fa-solid fa-download -ml-1 mr-2 h-5 w-5"></i>
                    Download Barcodes
            </a>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form action="<?= base_url('admin/produk') ?>" method="get" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full md:w-auto">
                <label for="filter_kategori" class="block text-sm font-medium text-gray-700 mb-1">Filter Kategori:</label>
                <div class="flex gap-2">
                    <select name="kategori" id="filter_kategori" 
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">-- Tampilkan Semua Kategori --</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= ($selectedKategori == $kat['id']) ? 'selected' : '' ?>>
                                <?= esc($kat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100">
                        <i class="fa-solid fa-filter mr-2 text-gray-500"></i> Filter
                    </button>

                    <?php if(!empty($selectedKategori)): ?>
                        <a href="<?= base_url('admin/produk') ?>" class="inline-flex items-center px-4 py-2 border border-red-200 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100" title="Reset Filter">
                            <i class="fa-solid fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-sm text-gray-500 mt-2 md:mt-4 ml-auto">
                Menampilkan <strong><?= count($produk) ?></strong> produk
            </div>
        </form>
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

    <div class="md:hidden space-y-4 mb-6">
        <?php if (empty($produk)): ?>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 text-center text-gray-500">
                <?php if(!empty($selectedKategori)): ?>
                    Tidak ada produk di kategori ini.
                <?php else: ?>
                    Belum ada data produk.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($produk as $p): ?>
                <?php
                    // Logic Foto (Copied for Mobile)
                    $fotoName = $p['foto_produk'];
                    $placeholder = base_url('assets/images/placeholder_product.png');
                    $fotoUrl = !empty($fotoName) ? base_url('uploads/produk/' . $fotoName) . '?v=' . time() : $placeholder;
                ?>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <img class="h-16 w-16 rounded-lg object-cover border border-gray-100" 
                                 src="<?= $fotoUrl ?>" 
                                 alt="<?= esc($p['nama_produk']) ?>"
                                 onerror="this.onerror=null;this.src='<?= $placeholder ?>';">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h3 class="text-sm font-bold text-gray-900 line-clamp-2"><?= esc($p['nama_produk']) ?></h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= !empty($p['nama_kategori']) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= esc($p['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Barcode: <?= esc($p['barcode'] ?? '-') ?></p>
                            
                            <div class="mt-3 grid grid-cols-2 gap-y-2 gap-x-4 text-xs">
                                <div>
                                    <span class="text-gray-500 block">Harga Jual</span>
                                    <span class="font-bold text-blue-600">Rp <?= number_format($p['harga'], 0, ',', '.') ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Stok</span>
                                    <span class="font-bold <?= $p['stok'] <= 5 ? 'text-red-600' : 'text-gray-700' ?>">
                                        <?= esc($p['stok']) ?> pcs
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Rak</span>
                                    <span class="text-gray-700 font-medium"><?= esc($p['posisi_rak'] ?? '-') ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Exp Date</span>
                                    <span class="text-gray-700"><?= esc($p['exp_date'] ? date('d M Y', strtotime($p['exp_date'])) : '-') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <a href="<?= base_url('admin/produk/show/' . $p['id']) ?>" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fa-solid fa-eye mr-2"></i> Detail
                        </a>
                        <a href="<?= base_url('admin/produk/' . $p['id'] . '/edit') ?>" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-yellow-300 shadow-sm text-xs font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                            <i class="fa-solid fa-pencil-alt mr-2"></i> Edit
                        </a>
                        <form action="<?= base_url('admin/produk/' . $p['id']) ?>" method="post" class="inline" 
                              onsubmit="confirmAction(event, this, 'Hapus Produk?', 'Yakin hapus <?= esc($p['nama_produk']) ?>?', 'Ya, Hapus!')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE"> 
                            <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                <i class="fa-solid fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="hidden md:block flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">H. Beli</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">H. Jual</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exp Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rak</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($produk)): ?>
                                <tr>
                                    <td colspan="10" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <?php if(!empty($selectedKategori)): ?>
                                            Tidak ada produk di kategori ini.
                                        <?php else: ?>
                                            Belum ada data produk.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($produk as $p): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $no++ ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php
                                                    $fotoName = $p['foto_produk'];
                                                    $placeholder = base_url('assets/images/placeholder_product.png');
                                                    
                                                    if (!empty($fotoName)) {
                                                        // Tambahkan versi waktu agar browser me-refresh cache
                                                        $fotoUrl = base_url('uploads/produk/' . $fotoName) . '?v=' . time(); 
                                                    } else {
                                                        $fotoUrl = $placeholder;
                                                    }
                                                ?>
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded object-cover" 
                                                         src="<?= $fotoUrl ?>" 
                                                         alt="Foto" 
                                                         onerror="this.onerror=null;this.src='<?= $placeholder ?>';">
                                                </div>
                                                <div class="ml-4 text-sm font-medium text-gray-900 truncate max-w-[150px]" title="<?= esc($p['nama_produk']) ?>">
                                                    <?= esc($p['nama_produk']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= esc($p['barcode'] ?? '-') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full <?= !empty($p['nama_kategori']) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>">
                                                <?= esc($p['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Rp <?= number_format($p['harga_beli'] ?? 0, 0, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                            Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?= esc($p['stok']) ?> pcs
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?= esc($p['exp_date'] ? date('d M Y', strtotime($p['exp_date'])) : '-') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?= esc($p['posisi_rak'] ?? '-') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            
                                            <a href="<?= base_url('admin/produk/show/' . $p['id']) ?>" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-500 hover:bg-blue-600">
                                                <i class="fa-solid fa-eye h-4 w-4"></i>
                                            </a>
                                            
                                            <a href="<?= base_url('admin/produk/' . $p['id'] . '/edit') ?>" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600">
                                                <i class="fa-solid fa-pencil-alt h-4 w-4"></i>
                                            </a>

                                            <form action="<?= base_url('admin/produk/' . $p['id']) ?>" method="post" class="inline" 
                                                  onsubmit="confirmAction(event, this, 'Hapus Produk?', 'Apakah Anda yakin ingin menghapus produk <?= esc($p['nama_produk']) ?>? Ini tidak dapat dibatalkan.', 'Ya, Hapus!')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE"> 
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                                    <i class="fa-solid fa-trash-alt h-4 w-4"></i>
                                                </button>
                                            </form>
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
<?= $this->endSection() ?>