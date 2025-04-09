<?php

namespace App\Models;

use App\Models\berita;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class kategori_berita extends Model
{

    protected $primaryKey='id_kategori_berita';
    protected $table='kategori_beritas';
    protected $keyType="string";

    protected $fillable = [ 
        'id_kategori_berita',
        'kategori'
    ];

    public function berita() {
        return $this->hasMany(berita::class , 'id_kategori_berita' , 'id_kategori_berita');
    }
}
