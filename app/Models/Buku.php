<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Kategori;

class Buku extends Model
{
   protected $table = 'buku';
   public $timestamps = false;
    protected $fillable = ['idkategori', 'kode', 'judul', 'pengarang'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
