<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;

Route::post("/pengguna" , [PenggunaController::class , 'index']);
Route::post("/pengguna/login" , [PenggunaController::class , 'login']);
Route::get("/pengguna/saatIni" , [PenggunaController::class , 'currentPengguna']);
Route::patch("/pengguna/{slugPengguna}" , [PenggunaController::class , 'updatePengguna']);
Route::get("/penggunahs" , function(){
    dd('hm');
});
Route::delete("/pengguna/logout", [PenggunaController::class , 'logout']);