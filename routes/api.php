<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EmailMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\JurnalisMiddleware;
use App\Http\Middleware\PenggunaMiddleware;
use App\Http\Controllers\JurnalisController;
use App\Http\Controllers\PenggunaController;
use App\Http\Middleware\UniversalMiddleware;
use App\Http\Middleware\JurnalisActiveMiddleware;

                //ROUTE PENGGUNA
Route::post("/pengguna" , [PenggunaController::class , 'index']);
Route::post("/pengguna/login" , [PenggunaController::class , 'login']);

        //email
Route::post("/gantiPassword" , [PenggunaController::class , 'sendEmail']);
                // CHANGE PASS WITHOUT AUTH
Route::post("/pengguna/token-check/{token}" , [PenggunaController::class , 'checkToken']);
            //edit pass final
Route::patch("/lupa-password/{token}" , [PenggunaController::class , 'forgetPassword']);

Route::get('/berita/pengguna', [NewsController::class , 'allNews']);

// Counter visitor
Route::get('/berita/{kategori}/{slug}', [NewsController::class , 'counter']);

// popular news this weeks
Route::get('/berita/populer' ,[NewsController::class , 'popularNews']);

// Related News
 Route::get('/news/related/{kategori}' , [NewsController::class , 'relatedNews']);
        
 Route::middleware(EmailMiddleware::class)->group(function () {
      // sending email
      Route::post("/auth/gantiPasswordPengguna" , [PenggunaController::class , 'sendEmailAuth']);

      //Check Auth and token
      Route::get('/auth/token-ganti-password/{token}' , [PenggunaController::class , 'PassAuthView']);
  
      // simpan password Auth
     Route::patch("/auth/lupa-password-store/{token}" , [PenggunaController::class , 'storeNewPassword']);
     
 });
Route::middleware(PenggunaMiddleware::class)->group(function(){
    Route::get("/pengguna/saatIni" , [PenggunaController::class , 'currentPengguna']);
    Route::post("/pengguna/{slugPengguna}" , [PenggunaController::class , 'updatePengguna']);
    Route::post('/pengguna/simpanBerita/{slugBerita}' , [PenggunaController::class , 'saveNews']);
    Route::get('/pengguna/simpanBerita' , [PenggunaController::class , 'getSaveNews']);
    Route::delete('/pengguna/hapusSimpanBerita/{slugBerita}' , [PenggunaController::class , 'deleteNews']);
    Route::delete("/pengguna/logout", [PenggunaController::class , 'logout']);
    // MAIN  FEATURE
   
});


//          ROUTE ADMINISTRATOR - PUBLISHER
Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware(AdminMiddleware::class)->group(function () {
    Route::get('/admin' , [AdminController::class , 'currentAdmin']);
    Route::delete('/admin/logout' , [AdminController::class , 'logout']);

    Route::post('/jurnalis/active/{slugJurnalis}' , [AdminController::class , 'updateActive']);
    Route::get('/admin/jurnalis/search' , [AdminController::class , 'searchJurnalis'] );

    Route::get('/admin/jurnalis/{slugAdmin}' , [AdminController::class , 'showData'] );

    // Main Feature
    Route::get('/berita/{slugBerita}', [NewsController::class , 'showNews']);
    
     Route::post('/kategoriBerita', [NewsController::class , 'storeCategory']);
     Route::get('/kategoriBerita/{idKatBe}' , [NewsController::class , 'detailKategori']);
     Route::patch('/kategoriBerita/{idKatBe}' , [NewsController::class , 'updateKategori']);
     Route::delete('/kategoriBerita/{idKatBe}' , [NewsController::class , 'deleteKategori']);
});

//          ROUTE JURNALIS
Route::post('/jurnalis/login' , [JurnalisController::class , 'login']);
Route::post('/jurnalis/register' , [JurnalisController::class , 'register']);

Route::middleware(JurnalisMiddleware::class)->group(function () {
  
    Route::get('/jurnalis/{slugAdmin}' , [AdminController::class , 'showData'] );
    Route::delete('/jurnalis/logout' , [AdminController::class , 'logout']);
    
    Route::middleware(JurnalisActiveMiddleware::class)->group(function(){
     // MAIN FEATURE
        Route::post('/jurnalis/addNews' , [NewsController::class , 'storeNews']);
        Route::post('/jurnalis/updateNews/{slugBerita}' ,[NewsController::class , 'updateNews'] );
        Route::get('/jurnalis/berita/{slugBerita}',[NewsController::class , 'showNews']);
    });
  
});

Route::middleware(UniversalMiddleware::class)->group(function(){
     Route::get('/berita', [NewsController::class , 'index']);

     Route::get('/kategoriBerita', [NewsController::class , 'showCategory']);

    Route::get('/administrator' , [AdminController::class , 'currentAdmin']);
    
    Route::post('/administrator/update/{slugAdmin}' , [AdminController::class , 'updateData']);
     
     // SOFT DELETE
     Route::post('/beritaJurnalis/delete/{slugBerita}' ,[NewsController::class , 'softDelete']);
    
     //lihat tong sampah
     Route::get('/beritaJurnalis/tong-sampah' ,[NewsController::class , 'trashBin']);
 
     //Hapus Permanen
     Route::post('/beritaJurnalis/deleteForce/{slugBerita}' , [NewsController::class , 'delete']);
 
     // restore data
     Route::post('/beritaJurnalis/restore/{slugBerita}' , [NewsController::class , 'restoreNews']);
});
