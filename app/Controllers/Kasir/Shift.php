<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\CashRegisterModel;
use App\Models\PenjualanModel;
use App\Models\UserModel;

class Shift extends BaseController
{
    protected $shiftModel;
    protected $penjualanModel;

    public function __construct()
    {
        $this->shiftModel = new CashRegisterModel();
        $this->penjualanModel = new PenjualanModel();
    }

    public function open()
    {
        $userId = session()->get('user_id');
        if ($this->shiftModel->getActiveShift($userId)) {
            return redirect()->to('/kasir');
        }
        return view('kasir/shift/open_register', ['title' => 'Buka Kasir']);
    }

    public function processOpen()
    {
        $rules = ['modal_awal' => 'required|numeric'];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Modal awal harus berupa angka.');
        }

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user && $user['role'] === 'kasir') {
            $currentTime = date('H:i:s');
            // PERBAIKAN: Sesuaikan dengan nama kolom database yang benar (jam_mulai & jam_selesai)
            $startTime = $user['jam_mulai'] ?? '00:00:00';
            $endTime   = $user['jam_selesai'] ?? '23:59:59';

            // Logika pengecekan shift malam (jika jam mulai > jam selesai, misal 22:00 ke 06:00)
            $isAllowed = ($startTime < $endTime) 
                ? ($currentTime >= $startTime && $currentTime <= $endTime)
                : ($currentTime >= $startTime || $currentTime <= $endTime);

            if (!$isAllowed) {
                return redirect()->back()->with('error', 
                    "Gagal Buka Shift! Anda berada di luar jam kerja ($startTime s/d $endTime)."
                );
            }
        }

        $this->shiftModel->save([
            'user_id'    => $userId,
            'modal_awal' => $this->request->getPost('modal_awal'),
            'status'     => 'open',
            'opened_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/kasir')->with('success', 'Shift kasir berhasil dibuka.');
    }

    public function autoCloseShift(array $shift)
    {
        $userId = $shift['user_id'];
        
        // Hitung total penjualan tunai
        $totalCashSales = $this->penjualanModel
            ->selectSum('total_harga')
            ->where('id_user', $userId)
            ->where('created_at >=', $shift['opened_at'])
            ->where('metode_pembayaran', 'cash')
            ->first()['total_harga'] ?? 0;
            
        $expectedCash = $shift['modal_awal'] + $totalCashSales;

        $totalOmset = $this->penjualanModel
            ->selectSum('total_harga')
            ->where('id_user', $userId)
            ->where('created_at >=', $shift['opened_at'])
            ->first()['total_harga'] ?? 0;

        $this->shiftModel->update($shift['id'], [
            'total_penjualan_sistem' => $totalOmset,
            'total_uang_fisik'       => $expectedCash,
            'status'                 => 'closed',
            'closed_at'              => date('Y-m-d H:i:s'),
            'catatan'                => 'SISTEM: Ditutup otomatis karena melewati batas waktu toleransi shift.'
        ]);
        
        if (function_exists('log_activity')) {
            log_activity('auto_close_shift', 'Shift ID ' . $shift['id'] . ' ditutup paksa oleh sistem.');
        }
    }

    public function close()
    {
        $userId = session()->get('user_id');
        $activeShift = $this->shiftModel->getActiveShift($userId);

        if (!$activeShift) {
            return redirect()->to('/kasir')->with('error', 'Tidak ada shift yang aktif saat ini.');
        }

        // Ambil rincian omset berdasarkan metode pembayaran
        $summary = $this->penjualanModel
            ->select('metode_pembayaran, SUM(total_harga) as nominal, COUNT(id) as qty')
            ->where('id_user', $userId)
            ->where('created_at >=', $activeShift['opened_at'])
            ->groupBy('metode_pembayaran')
            ->findAll();

        $totalCashSales = 0;
        $totalNonCash = 0;
        $totalTransactions = 0;

        foreach ($summary as $row) {
            $totalTransactions += $row['qty'];
            if ($row['metode_pembayaran'] == 'cash') {
                $totalCashSales += $row['nominal'];
            } else {
                $totalNonCash += $row['nominal'];
            }
        }

        $data = [
            'title'             => 'Tutup Kasir',
            'shift'             => $activeShift,
            'summary'           => $summary,
            'totalCashSales'    => $totalCashSales,
            'totalNonCash'      => $totalNonCash,
            'totalTransactions' => $totalTransactions,
            'expectedCash'      => $activeShift['modal_awal'] + $totalCashSales
        ];

        return view('kasir/shift/close_register', $data);
    }

    public function processClose()
    {
        $userId = session()->get('user_id');
        $shift = $this->shiftModel->getActiveShift($userId);
        
        if (!$shift) return redirect()->to('/kasir');

        $uangFisik = $this->request->getPost('uang_fisik');
        if (!is_numeric($uangFisik)) {
            return redirect()->back()->with('error', 'Input uang fisik harus berupa angka.');
        }

        $sales = $this->penjualanModel
            ->selectSum('total_harga')
            ->where('id_user', $userId)
            ->where('created_at >=', $shift['opened_at'])
            ->first();
            
        // Simpan data penutupan dan arahkan ke logout
        $this->shiftModel->update($shift['id'], [
            'total_penjualan_sistem' => $sales['total_harga'] ?? 0,
            'total_uang_fisik'       => $uangFisik,
            'status'                 => 'closed',
            'closed_at'              => date('Y-m-d H:i:s'),
            'catatan'                => $this->request->getPost('catatan')
        ]);

        // Hancurkan session khusus shift sebelum logout jika perlu
        // session()->remove('is_shift_active'); 

        return redirect()->to('/logout')->with('success', 'Kasir telah ditutup. Terima kasih atas kerja keras Anda hari ini!');
    }
}