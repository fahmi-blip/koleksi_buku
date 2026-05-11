<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VisitLog;

class Toko extends Model
{
    protected $table = 'toko';

    protected $fillable = [
        'barcode',
        'name',
        'latitude',
        'longitude',
        'accuracy',
    ];

    public function visits()
    {
        return $this->hasMany(VisitLog::class);
    }
}
