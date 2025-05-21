<?php

namespace App\Models;

use App\Models\PasswordResetToken;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Model implements Authenticatable
{
    use HasFactory;
    
    use Sluggable;
    protected $table = "pengguna";
    protected $primaryKey = "id_pengguna";
    protected $keyType="string";

    protected $fillable = [
        'id_pengguna',
        'slug',
        'nama',
        'email',
        'gambar' ,
        'password',
        'alamat',
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

    public function passwordResetTokens(){
        return $this->morphMany(PasswordResetToken::class , 'resettable');
    }


    public function getAuthIdentifierName(){
        return 'nama';
}

public function getAuthIdentifier(){
        return $this->id_pengguna;
}

public function getAuthPassword(){
        return $this->password;
}

public function getAuthPasswordName(){
    return 'password';
}

public function getRememberToken(){
    return null;
}

public function setRememberToken($value){

}

public function getRememberTokenName(){
    return null;
}

}

