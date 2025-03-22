<?php

namespace App\Models;

use App\Models\berita;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'beritas'; 

    protected $primaryKey = 'id_berita'; 

    public $incrementing = false; 
    use Sluggable;
    protected $fillable = [
        'id_berita',
        'id_administrator',
        'slug',
        'judul_berita',
        'deks_berita',
        'is_tayang',
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
        return $this->hasMany(kategori_berita::class, 'id_berita', 'id_berita');
    }


    public function gambar_berita()
    {
        return $this->hasMany(gambar_berita::class, 'id_berita', 'id_berita');
    }
}
