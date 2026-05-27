<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Memanggil fungsi Database Laravel

class AuthController extends Controller
{
    public function prosesLogin(Request $request)
    {
        // 1. Tangkap data yang diketik user di form
        $username = $request->input('username');
        $password = $request->input('password');

        // 2. Cek ke tabel 'user' di database
        $user = DB::table('user')
                  ->where('username', $username)
                  ->where('password', $password)
                  ->first();

        // 3. Logika penentuan arah
        if ($user) {
            // Jika sukses, buat session seperti biasa
            session(['username' => $user->username]);
            
            // PERBAIKAN: Jangan langsung ke dashboard, tapi kembali ke /login dulu bawa pesan 'success'
            return redirect('/login')->with('success', 'Selamat datang kembali, ' . $user->username . '!');
        } else {
            // Jika gagal, tetap kembali ke /login bawa pesan 'error'
            return redirect('/login')->with('error', 'Username atau password yang Anda masukkan salah.');
        }
    }

    public function logout()
    {
        // 1. Menghapus semua data session (Sama seperti session_destroy)
        session()->flush();

        // 2. Lempar kembali ke halaman login
        return redirect('/login');
    }
}