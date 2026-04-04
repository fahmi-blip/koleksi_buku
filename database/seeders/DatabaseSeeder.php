<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vendor = Vendor::firstOrCreate([
            'nama_vendor' => 'Kantin Utama',
        ]);

        User::updateOrCreate(
            ['email' => 'combyn559@gmail.com'],
            [
                'name' => 'Vendor Kantin',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_id' => $vendor->idvendor,
                'email_verified_at' => now(),
            ]
        );

        Menu::firstOrCreate(
            [
                'idvendor' => $vendor->idvendor,
                'nama_menu' => 'Nasi Goreng Spesial',
            ],
            [
                'harga' => 18000,
                'path_gambar' => null,
                'is_available' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'idvendor' => $vendor->idvendor,
                'nama_menu' => 'Es Teh Manis',
            ],
            [
                'harga' => 5000,
                'path_gambar' => null,
                'is_available' => true,
            ]
        );
    }
}
