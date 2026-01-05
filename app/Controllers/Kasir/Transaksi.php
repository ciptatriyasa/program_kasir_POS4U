<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\CashRegisterModel;
use App\Models\UserModel; // Tambahkan Import Model User

class Transaksi extends BaseController
{
    protected $produkModel;
    protected $penjualanModel;
    protected $detailModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->penjualanModel = new PenjualanModel();
        $this->detailModel = new DetailPenjualanModel();
    }

    public function process()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);

        $cartItems = json_decode($this->request->getPost('cart_items'), true);
        $userId = session()->get('user_id');
        $metodePembayaran = $this->request->getPost('metode_pembayaran') ?? 'cash';
        $uangDibayar = (float) $this->request->getPost('uang_dibayar');
        $kembalian = (float) $this->request->getPost('kembalian');

        if (empty($cartItems) || !$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak valid.', 'csrf_hash' => csrf_hash()]);
        }

        // --- VALIDASI 1: Sinkronisasi Jam Kerja (Poin 2) ---
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user && $user['role'] === 'kasir') {
            if (!empty($user['jam_mulai']) && !empty($user['jam_selesai'])) {
                $now = time();
                $jam_mulai = strtotime($user['jam_mulai']);
                $jam_selesai = strtotime($user['jam_selesai']);

                // Logika pengecekan shift (mendukung shift malam yang melewati tengah malam)
                $isAllowed = ($jam_mulai < $jam_selesai) 
                    ? ($now >= $jam_mulai && $now <= $jam_selesai)
                    : ($now >= $jam_mulai || $now <= $jam_selesai);

                if (!$isAllowed) {
                    return $this->response->setJSON([
                        'status' => 'error', 
                        'message' => 'Waktu shift kerja Anda telah habis. Transaksi ditolak oleh sistem.', 
                        'csrf_hash' => csrf_hash()
                    ]);
                }
            }
        }

        // --- VALIDASI 2: Shift Kasir Aktif (Cash Register) ---
        $shiftModel = new CashRegisterModel();
        $activeShift = $shiftModel->getActiveShift($userId);

        if (!$activeShift) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Shift belum dibuka! Silakan buka kasir (Open Shift) terlebih dahulu sebelum transaksi.', 
                'csrf_hash' => csrf_hash()
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $serverTotal = 0;
            $detailData = [];
            $productIds = array_column($cartItems, 'id');

            if (empty($productIds)) throw new \Exception('Keranjang kosong.');

            // Lock for update (Mencegah Race Condition - Poin 5)
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $sql = "SELECT * FROM produk WHERE id IN ({$placeholders}) FOR UPDATE";
            $productsInDb = $db->query($sql, $productIds)->getResultArray();
            $productsMap = array_column($productsInDb, null, 'id');

            foreach ($cartItems as $item) {
                $productId = $item['id'];
                $qty = (int) $item['qty'];

                if (!isset($productsMap[$productId])) throw new \Exception("Produk ID $productId tidak ditemukan.");
                $product = $productsMap[$productId];

                if ($product['stok'] < $qty) throw new \Exception("Stok {$product['nama_produk']} tidak mencukupi.");

                $subtotal = (float)$product['harga'] * $qty;
                $serverTotal += $subtotal;

                // Simpan Snapshot Harga Beli untuk Profit/Loss
                $detailData[] = [
                    'id_produk' => $productId,
                    'jumlah'    => $qty,
                    'subtotal'  => $subtotal,
                    'harga_beli_snapshot' => $product['harga_beli']
                ];
                
                $this->produkModel->decrementStock($productId, $qty);
            }

            if ($metodePembayaran !== 'cash') {
                $uangDibayar = $serverTotal;
                $kembalian = 0;
            } elseif ($uangDibayar < $serverTotal) {
                throw new \Exception("Uang yang dibayarkan kurang.");
            }

            // Simpan Data Penjualan Utama
            $this->penjualanModel->insert([
                'id_user' => $userId,
                'total_harga' => $serverTotal,
                'metode_pembayaran' => $metodePembayaran,
                'uang_dibayar' => $uangDibayar,
                'kembalian' => $kembalian,
                'tanggal_penjualan' => date('Y-m-d H:i:s'),
            ]);
            $penjualanId = $this->penjualanModel->insertID();

            // Pasangkan ID Penjualan ke baris detail
            foreach ($detailData as &$detail) {
                $detail['id_penjualan'] = $penjualanId;
            }
            $this->detailModel->insertBatch($detailData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan transaksi ke database.");
            }

            return $this->response->setJSON([
                'status' => 'success',
                'invoice_id' => $penjualanId,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function receipt($id)
    {
        // Menggunakan join withDeleted jika perlu (Poin 3)
        $penjualan = $this->penjualanModel
            ->select('penjualan.*, users.nama_lengkap as kasir_nama')
            ->join('users', 'users.id = penjualan.id_user', 'left')
            ->find($id);

        if (!$penjualan) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $detail = $this->detailModel
            ->select('detail_penjualan.*, produk.nama_produk')
            ->join('produk', 'produk.id = detail_penjualan.id_produk', 'left')
            ->where('id_penjualan', $id)
            ->findAll();

        return view('kasir/receipt', [
            'title' => 'Struk #' . $id,
            'penjualan' => $penjualan,
            'detail' => $detail
        ]);
    }
}