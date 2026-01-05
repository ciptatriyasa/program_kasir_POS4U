<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Register extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    public function save()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'username'     => 'required|min_length[5]|is_unique[users.username]',
            'password'     => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/register', ['validation' => $this->validator]);
        }

        $this->userModel->save([
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'username'     => $this->request->getVar('username'),
            'password'     => $this->request->getVar('password'),
            'role'         => 'kasir' // Default role
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}