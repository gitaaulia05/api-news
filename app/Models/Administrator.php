<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\PasswordResetToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrator extends Model  implements Authenticatable
{
    use HasFactory;
    use sluggable;
    protected $primaryKey = "id_administrator";
    protected $table = "administrators";
    protected $keyType="string";

    protected $fillable = [
        'id_administrator',
        'slug',
        'nama',
        'gambar',
        'email',
        'password', 
        'active',
        'role', 
        'token'
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
        return $this->id_administrator;
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
