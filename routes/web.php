<?php

use App\Mail\NewsApi;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/info' , function(){
    dd(phpinfo());
});


Route::get('gantiPassword/{token}' , [PenggunaController::class , 'passView']);

Route::get('auth/gantiPassword/{token}' , [PenggunaController::class , 'PassAuthView']);

Route::post('/test-email' ,  [PenggunaController::class , 'changePassword'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;