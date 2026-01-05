<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        if ($role == 'admin') {
            return redirect()->to('/admin/dashboard');
        } elseif ($role == 'kasir') {
            return redirect()->to('/kasir/dashboard');
        } else {
            return redirect()->to('/login');
        }
    }
}