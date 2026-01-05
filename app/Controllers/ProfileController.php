<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
    }

    // Menampilkan halaman edit profil
    public function index()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Profil Saya',
            'user'  => $user,
            'validation' => \Config\Services::validation()
        ];
        
        // Perluas layout admin karena halaman profil menggunakan layout yang sama
        return view('profile/index', $data);
    }

    // Memproses update profil
    public function update()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        // Aturan validasi
        // Cek email unik HANYA jika emailnya berubah
        $emailRule = ($this->request->getVar('email') != $user['email'])
                     ? 'required|valid_email|is_unique[users.email]'
                     : 'required|valid_email';

        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => $emailRule,
            'no_hp'        => 'permit_empty|min_length[9]|alpha_numeric_punct',
            'alamat'       => 'permit_empty|min_length[10]',
            'foto_profil'  => [
                'label' => 'Foto Profil',
                'rules' => 'is_image[foto_profil]'
                           . '|mime_in[foto_profil,image/jpg,image/jpeg,image/png]'
                           . '|max_size[foto_profil,2048]', // Max 2MB
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 1. Ambil data
        $dataToUpdate = [
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'email'        => $this->request->getVar('email'),
            'no_hp'        => $this->request->getVar('no_hp'),
            'alamat'       => $this->request->getVar('alamat'),
        ];

        // 2. Proses Upload Foto (jika ada)
        $fotoFile = $this->request->getFile('foto_profil');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            // Hapus foto lama jika ada (bukan default)
            $oldFoto = $user['foto_profil'];
            if ($oldFoto && file_exists(FCPATH . 'uploads/avatars/' . $oldFoto)) {
                unlink(FCPATH . 'uploads/avatars/' . $oldFoto);
            }

            // Buat nama random
            $newName = $fotoFile->getRandomName();
            // Pindahkan ke folder public/uploads/avatars
            $fotoFile->move(FCPATH . 'uploads/avatars', $newName);
            
            $dataToUpdate['foto_profil'] = $newName;
        }

        // 3. Update data ke DB
        try {
            $this->userModel->update($userId, $dataToUpdate);
            
            // 4. Update session
            $this->session->set('nama_lengkap', $dataToUpdate['nama_lengkap']);
            if (isset($dataToUpdate['foto_profil'])) {
                $this->session->set('foto_profil', $dataToUpdate['foto_profil']);
            }

            log_activity('update_profile', 'User ID ' . $userId . ' memperbarui profilnya.');
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
            
        } catch (\Exception $e) {
            // Tangani jika ada error (misal email unik gagal)
            log_message('error', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Error: ' . $e->getMessage());
        }
    }
}