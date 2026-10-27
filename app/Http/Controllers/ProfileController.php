<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.confirmed'                => 'Konfirmasi password baru tidak sesuai.',
            'password.min'                      => 'Password baru minimal 6 karakter.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Catat ke Audit Log jika model tersedia
        if (class_exists(\App\Models\AuditLog::class)) {
            \App\Models\AuditLog::record('Mengubah password akun sendiri', 'user', auth()->id());
        }

        return back()->with('success', 'Password berhasil diubah!');
    }
}