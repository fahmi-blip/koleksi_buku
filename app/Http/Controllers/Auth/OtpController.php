<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function show()
    {
        if (!session()->has('pending_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('pending_otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi OTP telah habis. Silakan login kembali.']);
        }

        $user = User::find($userId);

        if (!$user || $user->otp !== strtoupper($request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
        }

        /** @var User $user */

        $user->otp = null;
        $user->save();

        Auth::login($user);

        $request->session()->regenerate();

        session(['user_session_data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'login_at' => now(),
        ]]);

        session()->forget('pending_otp_user_id');

        $defaultRoute = route('home', absolute: false);

        return redirect()->intended($defaultRoute);
    }
}
