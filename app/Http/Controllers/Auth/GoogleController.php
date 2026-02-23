<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Mengarahkan pengguna ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
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
                $user->update([
                    'id_google' => $googleUser->id,
                    'email_verified_at' => $user->email_verified_at ?? now(), // Otomatis verifikasi
                ]);
            }
        } else {
            // Jika user belum ada sama sekali, buat user baru
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'id_google' => $googleUser->id,
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                'email_verified_at' => now(), 
                'role' => 'customer',                
            ]);
        }

        // Generate OTP
        $otp = strtoupper(Str::random(6));
        $user->update(['otp' => $otp]);

        // Send Email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Store user ID in session for OTP verification
        session(['pending_otp_user_id' => $user->id]);

        return redirect()->route('otp.verify');
        
    } catch (\Exception $e) {
        // Ganti sementara redirect menjadi dd() untuk melihat jika ternyata ada error syntax / database
        dd($e->getMessage()); 
    }
}
}