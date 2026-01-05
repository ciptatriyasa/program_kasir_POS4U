<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form']);
    }

    /**
     * Helper baru untuk menentukan nama shift
     */
    private function getShiftName($jam_mulai)
    {
        if (empty($jam_mulai)) {
            return 'Fleksibel'; // Untuk user yang bisa login kapan saja
        }

        // Konversi ke jam (integer)
        $hour = (int) date('H', strtotime($jam_mulai));

        if ($hour >= 6 && $hour < 12) {
            return 'Shift Pagi';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Shift Siang';
        } elseif ($hour >= 17 || $hour < 6) { // Mencakup shift malam
            return 'Shift Malam';
        } else {
            return 'N/A';
        }
    }

    // Menampilkan daftar user
    public function index()
    {
        // Karena UserModel sudah disetel untuk Soft Delete, findAll() hanya akan mengambil user yang TIDAK terhapus.
        $users = $this->userModel->findAll(); 
        $dataUsers = [];

        // Tambahkan info 'nama_shift' ke setiap user
        foreach ($users as $user) {
            $user['nama_shift'] = $this->getShiftName($user['jam_mulai']);
            $dataUsers[] = $user;
        }

        $data = [
            'users' => $dataUsers, 
            'title' => 'Manajemen User'
        ];
        return view('admin/users/index', $data);
    }

    // Menampilkan halaman detail user
    public function show($id)
    {
        // find($id) otomatis mengabaikan user yang telah di-soft delete
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $data = [
            'user'       => $user,
            'nama_shift' => $this->getShiftName($user['jam_mulai']),
            'title'      => 'Detail User'
        ];
        return view('admin/users/show', $data); 
    }

    // Menampilkan form edit user
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $data = [
            'user'  => $user,
            'title' => 'Edit User'
        ];
        return view('admin/users/edit', $data);
    }

    // Memproses update data user
    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'role'      => 'required|in_list[admin,kasir]',
            'jam_mulai' => 'permit_empty|valid_date[H:i]', 
            'jam_selesai' => 'permit_empty|valid_date[H:i]' 
        ];
        if (!empty($this->request->getVar('password'))) {
            $rules['password']     = 'min_length[6]';
            $rules['pass_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->request->getVar('role');
        $clearShiftTimes = $this->request->getVar('clear_shift_times') === '1'; 

        $jam_mulai = null;
        $jam_selesai = null;

        if (!$clearShiftTimes) {
            $inputJamMulai = $this->request->getVar('jam_mulai');
            $inputJamSelesai = $this->request->getVar('jam_selesai');

            if (!empty($inputJamMulai) && !empty($inputJamSelesai)) {
                $timeMulai = strtotime($inputJamMulai);
                $timeSelesai = strtotime($inputJamSelesai);

                if ($timeMulai !== false && $timeSelesai !== false) {
                    $jam_mulai = date('H:i:s', $timeMulai);
                    $jam_selesai = date('H:i:s', $timeSelesai);
                } else {
                    return redirect()->back()->withInput()->with('errors', ['jam_kerja' => 'Format jam mulai atau jam selesai tidak valid.']);
                }
            } elseif (!empty($inputJamMulai) || !empty($inputJamSelesai)) {
                 return redirect()->back()->withInput()->with('errors', ['jam_kerja' => 'Harap isi kedua jam (mulai dan selesai) atau kosongkan keduanya.']);
            }
        }

        $dataToUpdate = [
            'role'        => $role,
            'jam_mulai'   => $jam_mulai,
            'jam_selesai' => $jam_selesai,
        ];

        $password = $this->request->getVar('password');
        if (!empty($password)) {
            $dataToUpdate['password'] = $password; 
        }

        $this->userModel->update($id, $dataToUpdate);

        return redirect()->to('/admin/users')->with('success', 'Data user berhasil diperbarui.');
    }

    // --- FUNGSI DELETE BARU: Menggunakan Soft Delete ---
    public function delete($id = null)
    {
        // 1. Cek agar tidak bisa hapus diri sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // 2. Cari user (find() hanya mengembalikan user yang belum di-soft delete)
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan.');
        }

        // 3. Lakukan Soft Delete
        try {
            // Memanggil delete() pada model yang mengaktifkan soft delete
            $this->userModel->delete($id);
            
            // Log aktivitas
            if (function_exists('log_activity')) {
                log_activity('soft_delete_user', 'Admin melakukan soft delete pada user: ' . $user['username'] . ' (ID: ' . $id . ')');
            }
            
            return redirect()->to('/admin/users')->with('success', 'User ' . esc($user['username']) . ' berhasil di-nonaktifkan (soft delete).');
        
        } catch (\Exception $e) {
            // Tangkap error umum jika terjadi kegagalan saat soft delete (misal: DB error)
            log_message('error', 'Gagal soft delete user ID ' . $id . ': ' . $e->getMessage());
            return redirect()->to('/admin/users')->with('error', 'Terjadi kesalahan saat menonaktifkan user.');
        }
    }
}