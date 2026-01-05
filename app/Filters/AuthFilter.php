<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // 1. Cek sesi dasar
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $session->get('user_id');
        
        if ($userId) {
            $userModel = new UserModel();
            
            // Ambil user termasuk data jam kerja dan status delete
            $user = $userModel->withDeleted()->find($userId); 

            // 2. Cek apakah user masih ada atau sudah di-soft delete
            if (!$user || $user['deleted_at'] !== null) {
                $session->destroy();
                return redirect()->to('/login')->with('error', 'Akun Anda telah dinonaktifkan.');
            }

            // 3. LOGIKA SHIFT KASIR (Modifikasi: Hapus Auto-Kick & Tambah Warning)
            if ($user['role'] === 'kasir') {
                if (!empty($user['jam_mulai']) && !empty($user['jam_selesai'])) {
                    $now = time();
                    $jam_mulai = strtotime($user['jam_mulai']);
                    $jam_selesai = strtotime($user['jam_selesai']);

                    // a. Cek Logika Shift (Normal vs Overnight)
                    $isAllowed = ($jam_mulai < $jam_selesai) 
                        ? ($now >= $jam_mulai && $now <= $jam_selesai)
                        : ($now >= $jam_mulai || $now <= $jam_selesai);

                    // b. Hitung Sisa Waktu (Dalam Menit)
                    $endTime = $jam_selesai;
                    
                    // Penyesuaian khusus jika shift "nyebrang hari" (misal 22:00 - 06:00)
                    // Jika sekarang jam 23:00 (lebih besar dari mulai), berarti selesainya besok (+1 hari)
                    if ($jam_mulai > $jam_selesai && $now >= $jam_mulai) {
                        $endTime += 86400; // Tambah 24 jam ke waktu selesai
                    }

                    $secondsLeft = $endTime - $now;
                    $minutesLeft = floor($secondsLeft / 60);

                    // --- LOGIKA BARU DI SINI ---
                    
                    if (!$isAllowed) {
                        // KASUS 1: Waktu Sudah Habis (OVERTIME)
                        // JANGAN destroy session, tapi beri peringatan keras agar segera tutup.
                        // Flashdata ini akan ditangkap oleh View (dashboard/kasir)
                        $session->setFlashdata('shift_warning', 'WAKTU SHIFT HABIS! Mohon segera selesaikan transaksi dan lakukan Tutup Shift.');
                        
                    } elseif ($minutesLeft <= 15 && $minutesLeft > 0) {
                        // KASUS 2: Sisa waktu <= 15 Menit
                        $session->setFlashdata('shift_warning', "Perhatian: Waktu shift Anda tersisa {$minutesLeft} menit lagi.");
                    }
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}