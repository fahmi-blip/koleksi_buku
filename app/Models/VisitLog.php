<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    protected $table = 'visit_logs';

    protected $fillable = [
        'toko_id',
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'distance',
        'threshold',
        'status',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
}
