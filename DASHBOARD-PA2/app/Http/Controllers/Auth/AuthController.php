<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $akun = Akun::with('guru')->where('username', $credentials['username'])->first();

        if (!$akun || !Hash::check($credentials['password'], $akun->password)) {
            return back()->withErrors([
                'username' => 'Username atau password salah',
            ])->onlyInput('username');
        }

        // Tentukan label role untuk ditampilkan
        $roleDisplay = $akun->role;
        if ($akun->role === 'guru' && $akun->guru) {
            $roleDisplay = $akun->guru->jabatan; // "Guru" atau "Kepala Sekolah"
        }

        // Set session
        session([
            'akun_id' => $akun->id_akun, 
            'id_guru' => $akun->id_guru,
            'role' => $akun->role, 
            'role_display' => $roleDisplay,
            'username' => $akun->username, 
            'is_super_admin' => $akun->is_super_admin
        ]);

        if ($akun->role === 'guru') {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return redirect()->route('dashboard.orangtua')->with('success', 'Login berhasil!');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logout berhasil');
    }
}
