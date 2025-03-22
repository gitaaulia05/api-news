<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class gambar_berita extends Model
{
  
        use HasFactory;
    
        protected $table = 'gambar_beritas'; 
    
        protected $primaryKey = 'id_gambar'; 
    
        public $incrementing = false; 
        use Sluggable;
        protected $fillable = [
            'id_gambar',
            'slug',
            'id_berita',
            'gambar_berita',
            'keterangan_gambar',
            'posisi_gambar'
        ];


        public function berita():BelongsTo
        {
            return $this->belongsTo(berita::class, 'id_berita', 'id_berita');
        }

        protected function getJudulBerita(){
            return optional($this->berita)->judul_berita;
        }

        public function sluggable(): array
        {
            return [
                'slug' => [
                    'source' => 'berita.judul_berita',
                    'onUpdate'=> true,
                ]
            ];
        }


}
