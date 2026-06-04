<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class AuthController extends Controller
{
    public function prosesLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Cek ke tabel 'users' di database
        $user = DB::table('users')
                  ->where('username', $username)
                  ->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Username atau password yang Anda masukkan salah.');
        }

        // Cek apakah password cocok (dengan encrypted password)
        try {
            $decryptedPassword = Crypt::decryptString($user->password);
            if ($decryptedPassword === $password) {
                // Password cocok
                session(['username' => $user->username, 'name' => $user->name]);
                return redirect('/login')->with('success', 'Selamat datang kembali, ' . $user->username . '!');
            } else {
                return redirect('/login')->with('error', 'Username atau password yang Anda masukkan salah.');
            }
        } catch (\Exception $e) {
            // Jika terjadi error saat decrypt (password lama yang di-hash)
            if (Hash::check($password, $user->password)) {
                session(['username' => $user->username, 'name' => $user->name]);
                return redirect('/login')->with('success', 'Selamat datang kembali, ' . $user->username . '!');
            } else {
                return redirect('/login')->with('error', 'Username atau password yang Anda masukkan salah.');
            }
        }
    }

    public function prosesRegister(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'name' => 'required',
            'hint' => 'required',
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'name.required' => 'Nama lengkap harus diisi',
            'hint.required' => 'Hint harus diisi',
        ]);

        // Cek apakah username sudah ada
        $existingUser = DB::table('users')
                         ->where('username', $request->input('username'))
                         ->first();

        if ($existingUser) {
            return redirect('/register')->with('error', 'Username sudah terdaftar. Gunakan username lain.');
        }

        // Buat user baru dengan encrypted password
        $user = User::create([
            'username' => $request->input('username'),
            'password' => Crypt::encryptString($request->input('password')),
            'name' => $request->input('name'),
            'hint' => $request->input('hint'),
            'email' => $request->input('username') . '@almex.local',
        ]);

        return redirect('/register')->with('success', 'Akun berhasil dibuat. Silakan login dengan username dan password Anda.');
    }

    public function prosesForgotPassword(Request $request)
    {
        $username = $request->input('username');
        $hint = $request->input('hint');

        // Cari user berdasarkan username dan hint
        $user = DB::table('users')
                  ->where('username', $username)
                  ->where('hint', $hint)
                  ->first();

        if (!$user) {
            return redirect('/forgot-password')->with('error', 'Username atau Hint tidak cocok.');
        }

        // Decrypt password
        try {
            $decryptedPassword = Crypt::decryptString($user->password);
        } catch (\Exception $e) {
            // Jika password lama (di-hash), tidak bisa di-decrypt
            return redirect('/forgot-password')->with('error', 'Terjadi kesalahan saat mengambil password. Silakan hubungi administrator.');
        }

        // Kirim password via session untuk ditampilkan di sweetalert
        return redirect('/forgot-password')->with('password_found', $decryptedPassword)->with('username', $user->username);
    }

    public function logout()
    {
        // Menghapus semua data session
        session()->flush();

        // Lempar kembali ke halaman login
        return redirect('/login');
    }
}
