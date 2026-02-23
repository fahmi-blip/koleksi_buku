<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Kami telah mengirimkan kode OTP ke email Anda. Silakan masukkan kode tersebut di bawah ini untuk melanjutkan login.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('otp.verify.post') }}">
        @csrf

        <!-- OTP -->
        <div>
            <x-input-label for="otp" :value="__('Kode OTP')" />
            <x-text-input id="otp" class="block w-full mt-1 text-2xl tracking-widest text-center" type="text" name="otp" required autofocus autocomplete="off" maxlength="6" placeholder="XXXXXX" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Verifikasi OTP') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>