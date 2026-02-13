<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Buku;

class Kategori extends Model
{
   protected $table = 'kategori';
   public $timestamps = false;
   protected $primaryKey = 'idkategori';
    protected $fillable = ['nama_kategori'];

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }
}
