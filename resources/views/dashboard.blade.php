<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    <h3 class="font-bold">Data dari Session:</h3>
    @if(session('user_session_data'))
        <ul>
            <li>Nama: {{ session('user_session_data')['name'] }}</li>
            <li>Email: {{ session('user_session_data')['email'] }}</li>
            <li>Waktu Login: {{ session('user_session_data')['login_at'] }}</li>
        </ul>
    @endif
</x-app-layout>
