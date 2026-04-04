<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPesanan;
use App\Models\Vendor;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'idpesanan';
    public $timestamps = false;

    protected $fillable = [
        'order_code',
        'user_id',
        'idvendor',
        'nama',
        'timestamp',
        'total',
        'metode_bayar',
        'status_bayar',
        'payment_reference',
        'gateway_payload',
        'paid_at',
        'midtrans_status',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'paid_at' => 'datetime',
        'gateway_payload' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'order_code';
    }

    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }
}