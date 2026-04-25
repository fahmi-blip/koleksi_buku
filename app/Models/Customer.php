<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'idcustomer';

    protected $fillable = [
        'nama',
        'alamat',
        'provinsi',
        'kota',
        'kecamatan',
        'kodepos_kelurahan',
        'foto_blob',
        'foto_blob_mime',
        'foto_path',
        'foto_link',
        'metode_foto',
    ];
}