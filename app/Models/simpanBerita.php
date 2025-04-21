<?php

namespace App\Models;

use App\Models\berita;
use Illuminate\Database\Eloquent\Model;

class simpanBerita extends Model
{
    protected $table = 'simpan_beritas'; 
    protected $primaryKey = 'id_simpan_berita'; 
    public $incrementing = false; 
    protected $keyType = 'string'; // atau 'int' kalau integer

    protected $fillable = [
        'id_simpan_berita',
        'id_pengguna',
        'id_berita',
        'slug'
    ];

    public function berita(){
        return $this->belongsTo(berita::class, 'id_berita', 'id_berita');
    }
}
