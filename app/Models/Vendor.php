<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;
use App\Models\Pesanan;

class Vendor extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'idvendor';

    protected $fillable = ['nama_vendor'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'idvendor', 'idvendor');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'vendor_id', 'idvendor');
    }
}