<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Mengarahkan pengguna ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Menangani callback/kembalian dari Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan id_google atau email
            $user = User::where('id_google', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Jika user sudah ada (misal sebelumnya daftar manual), update id_google nya
                if (!$user->id_google) {
                    $user->update(['id_google' => $googleUser->id]);
                }
                // Login user
                Auth::login($user);
            } else {
                // Jika user belum ada sama sekali, buat user baru
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'id_google' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)) // Buat password acak karena login menggunakan IdP
                ]);
                Auth::login($user);
            }

            return redirect()->intended('/'); // Sesuaikan dengan route halaman utama Anda
            
        } catch (Exception $e) {
            return redirect('/login')->with('status', 'Terjadi kesalahan saat login menggunakan Google.');
        }
    }
}