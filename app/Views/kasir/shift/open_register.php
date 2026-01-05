<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Buka Kasir</h2>
    <p class="text-gray-500 text-center mb-6">Halo, <?= session()->get('nama_lengkap') ?>. Silakan masukkan modal awal di laci.</p>

    <form action="<?= base_url('kasir/shift/open') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Modal Awal (Rp)</label>
            <input type="number" name="modal_awal" required min="0" value="0"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-xl font-bold">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
            Buka Register
        </button>
    </form>
</div>
<?= $this->endSection() ?>