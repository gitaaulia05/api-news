<?php

namespace App\Models;

use App\Models\berita;
use Illuminate\Database\Eloquent\Model;

class counter_berita extends Model
{
    protected $primaryKey='id_counter_berita';
    protected $table='counter_beritas';
    protected $ketType="string";

    protected $fillable=[
        'id_counter_berita',
        'id_berita',
        'ip_address'
    ];

    public function berita() : belongsTo {
        return $this->BelongsTo(berita::class , 'id_berita' ,'id_berita');
    }
}
