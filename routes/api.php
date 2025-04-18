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

                //ROUTE PENGGUNA
Route::post("/pengguna" , [PenggunaController::class , 'index']);
Route::post("/pengguna/login" , [PenggunaController::class , 'login']);

        //email
Route::post("/gantiPassword" , [PenggunaController::class , 'sendEmail']);
                // CHANGE PASS WITHOUT AUTH
Route::post("/pengguna/token-check/{token}" , [PenggunaController::class , 'checkToken']);
            //edit pass fineal
Route::patch("/lupa-password/{token}" , [PenggunaController::class , 'forgetPassword']);

Route::get('/berita/pengguna', [NewsController::class , 'allNews']);

// Counter visitor
Route::get('/berita/{kategori}/{slugBerita}', [NewsController::class , 'counter']);
// Route::middleware(['web'])->get('/berita/{kategori}/{slug}', [BeritaController::class, 'counter']);


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
    Route::patch("/pengguna/{slugPengguna}" , [PenggunaController::class , 'updatePengguna']);

    Route::delete("/pengguna/logout", [PenggunaController::class , 'logout']);

    // MAIN  FEATURE
   
   
});


//          ROUTE ADMINISTRATOR - PUBLISHER
Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware(AdminMiddleware::class)->group(function () {
    Route::get('/admin' , [AdminController::class , 'currentAdmin']);
    Route::post('/jurnalis/active/{slugJurnalis}' , [AdminController::class , 'updateActive']);
    Route::patch('/admin/update/{slugAdmin}' , [AdminController::class , 'updateData']);
    Route::get('/admin/jurnalis/search' , [AdminController::class , 'searchJurnalis'] );
    Route::get('/admin/jurnalis/{slugAdmin}' , [AdminController::class , 'showData'] );
    Route::delete('/admin/logout' , [AdminController::class , 'logout']);

    // Main Feature
     Route::get('/berita', [NewsController::class , 'index']);
    Route::get('/berita/{slugBerita}', [NewsController::class , 'showNews']);
});

//          ROUTE JURNALIS
Route::post('/jurnalis/login' , [JurnalisController::class , 'login']);
Route::post('/jurnalis' , [JurnalisController::class , 'register']);

Route::middleware(JurnalisMiddleware::class)->group(function () {
    Route::post('/jurnalis/register' , [JurnalisController::class , 'register']);
    
    Route::get('/jurnalis' , [AdminController::class , 'currentAdmin']);
    Route::post('/jurnalis/update/{slugAdmin}' , [AdminController::class , 'updateData']);
    Route::get('/jurnalis/{slugAdmin}' , [AdminController::class , 'showData'] );
    Route::delete('/jurnalis/logout' , [AdminController::class , 'logout']);

    // MAIN FEATURE
    Route::post('/jurnalis/addNews' , [NewsController::class , 'storeNews']);
    Route::post('/jurnalis/updateNews/{slugBerita}' ,[NewsController::class , 'updateNews'] );
    Route::get('/berita', [NewsController::class , 'index']);
    Route::get('/jurnalis/berita/{slugBerita}',[NewsController::class , 'showNews']);
    
    // SOFT DELETE
    Route::get('/berita/delete/{slugBerita}' ,[NewsController::class , 'softDelete']);
    
    //lihat tong sampah
    Route::get('/berita/tong-sampah' ,[NewsController::class , 'trashBin']);

    //Hapus Permanen
    Route::get('/berita/deleteForce/{slugBerita}' , [NewsController::class , 'delete']);

    // restore data
    Route::get('/berita/restore/{slugBerita}' , [NewsController::class , 'restoreNews']);

});
