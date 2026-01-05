<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url', 'log']);
    }

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function process()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Username tidak ditemukan.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah.');
        }

        // Cek Shift Kasir
        if ($user['role'] == 'kasir') {
            if (!empty($user['jam_mulai']) && !empty($user['jam_selesai'])) {
                $jam_mulai = strtotime($user['jam_mulai']);
                $jam_selesai = strtotime($user['jam_selesai']);
                $now = time(); 

                $isAllowed = ($jam_mulai < $jam_selesai) 
                    ? ($now >= $jam_mulai && $now <= $jam_selesai)
                    : ($now >= $jam_mulai || $now <= $jam_selesai); // Shift malam lintas hari

                if (!$isAllowed) {
                    return redirect()->back()->with('error', 'Anda login di luar jam kerja shift.');
                }
            }
        }

        // Set Session
        $sessionData = [
            'user_id'      => $user['id'],
            'nama_lengkap' => $user['nama_lengkap'],
            'username'     => $user['username'],
            'role'         => $user['role'],
            'foto_profil'  => $user['foto_profil'],
            'isLoggedIn'   => true,
        ];
        session()->set($sessionData);

        log_activity('user_login', 'User ' . $user['username'] . ' berhasil login.');

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}