<?php

namespace App\Services;

use Log;
use Illuminate\Support\Facades\Http;


class PasswordServices{
    
    public function tokenCheck($token){
        $response = Http::post('http://127.0.0.1:8000/api/pengguna/token-check/'. $token);
          return $response;
    }

}