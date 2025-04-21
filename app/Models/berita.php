<?php

namespace App\Models;

use App\Models\berita;
use App\Models\simpanBerita;
use Illuminate\Database\Eloquent\Model;
use Coderflex\Laravisit\Concerns\CanVisit;
use Cviebrock\EloquentSluggable\Sluggable;
use Coderflex\Laravisit\Concerns\HasVisits;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class berita extends Model implements CanVisit
{
    
    use HasFactory, Sluggable , HasVisits;
    use  SoftDeletes;


    protected $table = 'beritas'; 
    protected $primaryKey = 'id_berita'; 
    public $incrementing = false; 
    protected $keyType = 'string'; // atau 'int' kalau integer
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id_berita',
        'id_administrator',
        'id_kategori_berita',
        'slug',
        'judul_berita',
        'deks_berita',
        'is_tayang',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'judul_berita',
                'onUpdate'=> true,
            ]
        ];
    }

    public function kategori_berita()
    {
        return $this->belongsTo(kategori_berita::class, 'id_kategori_berita', 'id_kategori_berita');
    }


    public function gambar_berita()
    {
        return $this->hasMany(gambar_berita::class, 'id_berita', 'id_berita');
    }

    
    public function simpanBerita()
    {
        return $this->hasMany(simpanBerita::class, 'id_berita', 'id_berita');
    }


    public function getRouteKeyName()
    {
        return 'slug'; 
    }


}
