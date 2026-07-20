<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function index()
    {
        // Jika user sudah login, tidak perlu menampilkan form login lagi.
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        // Ambil input dari form login.
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        // Validasi sederhana agar email dan password tidak kosong.
        if ($email === '' || $password === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email dan password wajib diisi.');
        }

        $userModel = new UserModel();
        $user      = $userModel->where('email', $email)->first();

        // Cocokkan email dan password hash yang tersimpan di database.
        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email atau password tidak sesuai.');
        }

        if ((int) $user['is_active'] !== 1) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Akun belum aktif.');
        }

        // Simpan data penting ke session sebagai tanda user sudah login.
        session()->set([
            'userId'     => $user['id'],
            'userName'   => $user['name'],
            'userEmail'  => $user['email'],
            'userRole'   => $user['role'],
            'userPhoto'  => $user['photo'] ?? null,
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        // Hapus semua data session saat logout.
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }
}
