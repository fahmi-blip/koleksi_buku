<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPesanan;
use App\Models\Vendor;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'idmenu';

    protected $fillable = [
        'idvendor',
        'nama_menu',
        'harga',
        'path_gambar',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'idmenu', 'idmenu');
    }
}