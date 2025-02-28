<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Pengguna extends Model
{
    use Sluggable;
    protected $table = "pengguna";
    protected $primaryKey = "id_pengguna";
    protected $keyType="string";

    protected $fillable = [
        'id_pengguna',
        'slug',
        'nama',
        'email',
        'password',
        'alamat',
        'provinsi',
        'kode_pos',
        'pendidikan_terakhir',
        'pekerjaan',
        'token',
    ];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama',
                'onUpdate'=> true,
            ]
        ];
    }
}

