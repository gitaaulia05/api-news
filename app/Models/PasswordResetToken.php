<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = "password_reset_token";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $timestamps = false;

    protected $fillable = [
        "id",
        "token",
        "auth",
        "resettable_id",
        "resettable_type",
        "created_at"
    ];

    public function resettable(){
        return $this->morphTo();
    }
}
