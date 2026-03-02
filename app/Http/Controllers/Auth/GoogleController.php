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
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }

        public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        
        $user = User::where('id_google', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        if ($user) {
            if (!$user->id_google) {
                $user->update([
                    'id_google' => $googleUser->id,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'id_google' => $googleUser->id,
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                'email_verified_at' => now(), 
                'role' => 'customer',                
            ]);
        }

        $otp = strtoupper(Str::random(6));
        $user->update(['otp' => $otp]);

        Mail::to($user->email)->send(new OtpMail($otp));

        session(['pending_otp_user_id' => $user->id]);

        return redirect()->route('otp.verify');
        
    } catch (\Exception $e) {
        dd($e->getMessage()); 
    }
}
}