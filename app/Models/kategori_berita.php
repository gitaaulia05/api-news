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
    protected $ketType="string";

    protected $fillable = [ 
        'id_kategori_berita',
        'id_berita', 
        'kategori'
    ];

    public function berita() : belongsTo{
        return $this->belongsTo(berita::class , 'id_berita' , 'id_berita');
    }
}
