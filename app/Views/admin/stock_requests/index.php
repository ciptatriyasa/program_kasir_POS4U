<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($title ?? 'Verifikasi Permintaan Stok') ?></h1>
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir (ID)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Diminta</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada permintaan stok baru.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($requests as $req): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium"><?= $no++ ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?= esc(date('d M Y, H:i', strtotime($req['created_at']))) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?= esc($req['kasir_nama']) ?> (#<?= esc($req['id_user_kasir']) ?>)
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= esc($req['nama_produk']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                            <?= esc($req['jumlah_diminta']) ?> pcs
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                            <?= esc($req['alasan']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                                $statusClass = [
                                                    'pending'  => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                ][$req['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize <?= $statusClass ?>">
                                                <?= esc($req['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <?php if ($req['status'] === 'pending'): ?>
                                                
                                                <form action="<?= base_url('admin/stock-requests/process/' . $req['id']) ?>" method="post" class="inline" 
                                                      onsubmit="confirmAction(event, this, 'Setujui Permintaan?', 'Anda akan MENAMBAH <?= $req['jumlah_diminta'] ?> pcs stok untuk <?= $req['nama_produk'] ?>.', 'Ya, Setujui!')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                                        <i class="fa-solid fa-check h-4 w-4"></i> Setujui
                                                    </button>
                                                </form>

                                                <form action="<?= base_url('admin/stock-requests/process/' . $req['id']) ?>" method="post" class="inline" 
                                                      onsubmit="confirmAction(event, this, 'Tolak Permintaan?', 'Anda yakin ingin MENOLAK permintaan stok ini?', 'Ya, Tolak!')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                                        <i class="fa-solid fa-times h-4 w-4"></i> Tolak
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-400 italic">Sudah Diproses</span>
                                            <?php endif; ?>
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