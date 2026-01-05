<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Cek jika tidak login atau jika role bukan 'admin'
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            // Jika bukan admin, kembalikan ke dashboard utama
            // Controller Dashboard akan mengarahkan ke view yang sesuai (kasir/login)
            return redirect()->to('/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa setelah request
    }
}