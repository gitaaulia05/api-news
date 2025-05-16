<?php

namespace App\Http\Controllers;

use Log;
use App\Mail\NewsApi;
use App\Models\berita;
use App\Models\Pengguna;
use App\Mail\NewsApiAuth;
use Illuminate\Support\Str;
use App\Models\simpanBerita;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\PasswordResetToken;
use App\Services\PasswordServices;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EmailRequest;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\saveNewsRequest;
use App\Http\Resources\NewsCollection;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PenggunaResource;
use App\Http\Requests\PenggunaLoginRequest;
use App\Http\Requests\PenggunaCreateRequest;
use App\Http\Requests\PenggunaUpdateRequest;
use App\Http\Requests\PenggunaForgetPassRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PenggunaController extends Controller
{

    public function  __construct (PasswordServices $passwordService){
            $this->PasswordService = $passwordService;
    }

    public function index(PenggunaCreateRequest $request) : JsonResponse{
        $data=$request->validated();
        $data['id_pengguna'] = (String) Str::uuid();
        $data['password'] = Hash::make($data['password']);

        $pengguna = new Pengguna($data);
        $pengguna->save();
        return (new PenggunaResource($pengguna))->response()->setStatusCode(201);
    }

    public function currentPengguna(Request $request) : PenggunaResource{
        $pengguna = Auth::guard('pengguna')->user();
        return new PenggunaResource($pengguna);
    }

    public function login(PenggunaLoginRequest $request) : PenggunaResource{
        $data = $request->validated();
        $pengguna = Pengguna::where('email' , $data['email'])->first();

        if(!$pengguna || !Hash::check($data['password'] , $pengguna->password)) {
            throw new HttpResponseException(response([
                "errors" =>[
                    "message" => [
                        "Email atau Password Salah!"
                    ]
                ]
            ], 401));
        }
            $pengguna->token = (String) Str::uuid()->toString();

            $pengguna->save();

            return new PenggunaResource($pengguna);
        
    }

    public function saveNews(saveNewsRequest $request, $slugBerita) : JsonResponse {
        $tokenHeader = $request->bearerToken(); 
        $pengguna = pengguna::where('token', $tokenHeader)->first();
       
        $berita = berita::where('slug' , $slugBerita)->first();

        $simpanBerita = simpanBerita::where('slug' , $slugBerita)->where('id_pengguna' , $pengguna->id_pengguna)->exists();
        if(!$pengguna || !$berita){
            throw new HttpResponseException(response([
                "errors" =>[
                    "message" => [
                        "Pengguna atau Berita Tidak Ditemukan"
                    ]
                ]
            ], 401));
        }

        if($simpanBerita){
            throw new HttpResponseException(response([
                "errors" =>[
                    "message" => [
                        "Berita Sudah Tersimpan"
                    ]
                ]
            ], 401));
        }

        $data = $request->validated();
        $data['id_pengguna'] = $pengguna->id_pengguna;
        $data['id_berita'] =$berita->id_berita;
        $data['slug'] =$berita->slug;
        $data['id_simpan_berita'] = (String) Str::uuid();

        $saveNews = new simpanBerita($data);
        $saveNews->save();
        return response()->json([
            'data'=> 'Berita Berhasil Disimpan!'
        ]
            ,201);
    }

    public function getSaveNews(Request $request) : NewsCollection{
        $tokenHeader = $request->bearerToken();

        $pengguna = pengguna::where('token', $tokenHeader)->first(); 
       
        $pageNews= $request->input('page', 1);
        $size = $request->input('size' , 1);

        $judul = $request->input('judul_berita');

        $query = simpanBerita::with([
            'berita.kategori_berita',
            'berita.gambar_berita',
            'berita' => function($q) {
                $q->withTrashed();
            }
        ])->where('id_pengguna' , $pengguna->id_pengguna)
        ->whereHas('berita', function ($q) use ($judul, $pengguna) {
            $q->withTrashed(); 
            if ($judul) {
                $q->where('judul_berita', 'like', '%' . $judul . '%');
            }
        })
        ->whereHas('berita.kategori_berita' , function($q){
            $q->whereNotNull('id_kategori_berita');
        });
        
        $news = $query->paginate(perPage: $size, page: $pageNews);

        return new NewsCollection($news);
    }

    public function deleteNews(Request $request, $slugBerita) : JsonResponse{
        $token = $request->bearerToken();
        $pengguna = Pengguna::where('token' , $token)->first();
        
        $simpanBerita = simpanBerita::where('slug' , $slugBerita)->where('id_pengguna' , $pengguna->id_pengguna)->first();

        if(!$simpanBerita) {
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    "message" => [
                        "Berita Tidak Ditemukan"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $simpanBerita->delete();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function updatePengguna(PenggunaUpdateRequest $request , $slugPengguna) : PenggunaResource {
        $pengguna = Pengguna::where('slug' , $slugPengguna)->first();
        if(!$pengguna){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    "message" => [
                        "Pengguna Tidak Ditemukan"
                    ]
                ]
            ])->setStatusCode(404));
        }

        if($request->file('gambar')) {
            $image =  $request->file('gambar');
            // simpan gambar ke storeage

            // kalo ada gambar di path hapus
            if($pengguna->gambar && Storage::disk('public')->exists($pengguna->gambar)){
                Storage::disk('public')->delete($pengguna->gambar);
            }

            $path = $request->file('gambar')->store('gambarPengguna' , 'public');
           $pengguna->gambar = $path;
        }
 

        $data = $request->validated();
        unset($data['gambar']);
        $pengguna->fill($data);
        $pengguna->save();
            return (new PenggunaResource($pengguna));
    }

        public function sendEmail(EmailRequest $request) : JsonResponse {
            $email = $request->validated(); 
            $pengguna = Pengguna::where('email' , $email['email'])->first() ?? Administrator::where('email' , $email['email'])->first();

            if(!$pengguna){
            throw new HttpResponseException(response()->json([
                "data" => [
                        "message" =>  "Email Berhasil dikirim Ke email anda" ,
                        "email" => $email['email'],
                        "message2" => "Periksa Email Anda Untuk Reset Password"
                    ]
            ])->setStatusCode(200));
            }

         $passwordToken =  PasswordResetToken::updateOrCreate(
                [ 
                    'id' => (String) Str::uuid(),
                    'resettable_id' => $pengguna->getKey(),
                    'resettable_type' => get_class($pengguna),
                     'auth' => 'none'
                ],
                [
                 'token' => str_replace('-', '' ,(String) Str::uuid()),
                 'created_at' => now()
                ]
                
            );
            $owner = $passwordToken->resettable;
         
                $msg = $passwordToken['token'];
                $subject = "Ganti Password Akun Portal Berita WinniCode";

                     //sent email ke pengguna
                Mail::to($pengguna->email)->send(new NewsApi($msg, $subject));
                return  response()->json([
                    "data" => [
                        "message" =>  "Email Berhasil dikirim Ke email anda" ,
                        "email" => $pengguna->email,
                        "message2" => "Periksa Email Anda Untuk Reset Password"
                    ]
                ])->setStatusCode(200);

}

    public function sendEmailAuth(EmailRequest $request) : JsonResponse{
        $email = $request->validated(); 
        $pengguna = Pengguna::where('email' , $email['email'])->first() ?? Administrator::where('email' , $email['email'])->first();
        $user = $pengguna;

         if(!$user) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Email Tidak Sesuai dengan Akun yang Sedang Login !'
                    ]
                ]
            ])->setStatusCode(401));
         }

        $data = $request->validated();
         if(empty($data)){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'harus pake'
                    ]
                ]
            ])->setStatusCode(302));
         }

            if($user->email !== $data['email']){
                throw new HttpResponseException(response()->json([
                    'errors' => [
                        'message' => [
                            'Email Tidak Sesuai dengan Akun yang Sedang Login !'
                        ]
                    ]
                ])->setStatusCode(403));

            }

            $passwordToken =  PasswordResetToken::updateOrCreate(
                [ 
                    'id' => (String) Str::uuid(),
                    'resettable_id' => $user->getKey(),
                    'resettable_type' => get_class($user),
                    'auth' => 'auth'
                ],
                [
                 'token' => str_replace('-', '' ,(String) Str::uuid()),
                 'created_at' => now()
                ]
                
            );

                $msg = $passwordToken['token'];
                
                $subject = "Ganti Password Akun Portal Berita WinniCode";

                //sent email ke pengguna
                Mail::to($user->email )->send(new NewsApiAuth($msg, $subject));
            return  response()->json([
                "data" => [
                    "message" =>  "Email Berhasil dikirim Ke email anda" ,
                    "email" => $user->email,
                    "message2" => "Periksa Email Anda Untuk Reset Password"
                ]
            ])->setStatusCode(200);
}



// store without auth
    public function forgetPassword(PenggunaForgetPassRequest $request , $token) : JsonResponse{
        $password= PasswordResetToken::where('token' , $token)->first();
        $token = $request->header('Authorization');

        if($token && str_starts_with($token, 'Bearer ')){
            $token = substr($token, 7);
        }

        $pengguna = Pengguna::where('token' , $token)->first() ?? Administrator::where('token' , $token)->first();

        if(!$password ){
           throw new HttpResponseException(response()->json([
            "errors" => [
                "message" => [
                    "Halaman Tidak Valid, Kirim Kembali Kode Ke Email Anda!"
                ]
            ]
           ])->setStatusCode(404));
        }


        if($password->auth == 'auth' && $pengguna)
        {
            throw new HttpResponseException(response()->json([
             "errors" => [
                 "message" => [
                     "Halaman Tidak Valid"
                 ]
             ]
            ])->setStatusCode(404));
         }
        

        $owner = $password->resettable;
        $data = $request->validated();
         
        $owner->update([
            'password' => Hash::make($data['password']),
        ]);
        
        $password->delete();
        return response()->json([
            'data' => [
                'success' => true,
            'owner' => $owner->resettable_id,
            'type' => get_class($owner), 
            'role' => $owner instanceof App\Models\Administrator ? $owner->role : 'user'
            ]
        ])->setStatusCode(200);
    }

// Store with Auth
    public function storeNewPassword(PenggunaForgetPassRequest $request , $token) : JsonResponse {
        $tokenHeader = $request->header('Authorization'); 

    if($tokenHeader && str_starts_with($tokenHeader, 'Bearer ')){
            $tokenHeader = substr($tokenHeader, 7);
        }
        $password= PasswordResetToken::where('token' , $token)->first();
        $pengguna = Pengguna::where('token' , $tokenHeader)->first() ?? Administrator::where('token' , $tokenHeader)->first();

        if(!$password){
           throw new HttpResponseException(response()->json([
            "errors" => [
                "message" => [
                    "Halaman Tidak Valid, Kirim Kembali Kode Ke Email Anda!"
                ]
            ]
           ])->setStatusCode(404));
        }

        $owner = $password->resettable;
        
        if($owner instanceof Pengguna  && $owner->id_pengguna !== $pengguna->id_pengguna){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Halaman Tidak Valid!"
                    ]
                ]
               ])->setStatusCode(404));
        } elseif($owner instanceof Administrator  && $owner->id_administrator !== $pengguna->id_administrator){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Halaman Tidak Valid!"
                    ]
                ]
               ])->setStatusCode(404));
        }
       
        $data = $request->validated();
         
        $owner->update([
            'password' => Hash::make($data['password']),
        ]);
        
        $password->delete();
        return response()->json([
            'data' => true,
            'owner' => $owner->nama
        ])->setStatusCode(200);
    }

    public function logout(Request $request) :JsonResponse {
                $pengguna = Auth::guard('pengguna')->user();
                
                $pengguna->token = null;
                $pengguna->save();

                return response()->json([
                    "data" => true
                ])->setStatusCode(200);
    }


public function checkToken($token) : JsonResponse {
    $tokens = PasswordResetToken::where('token', $token)->first();
    if(empty($tokens) || $tokens->auth == "auth" ){
            return response()->json([
                "data" => false,
                "message" => "Halaman Tidak Valid, Kirim Kembali Kode Ke Email Anda!"
            ])->setStatusCode(404);
   
    }
        if(Carbon::parse($tokens->created_at)->addMinutes(10) > now()  && $tokens->auth !== "auth" ){
            return response()->json([
                "data" => true,
                "message" => $token
            ])->setStatusCode(200);
        } else {    
   
       return response()->json([
        "data" => false,
        "message" => "Halaman Tidak valid, Silahkan Kirim Tautan Kembali ke Email Anda"
    ])->setStatusCode(410);
        }
}

    public function passView($token) : JsonResponse{
        $tokens = PasswordResetToken::where('token', $token)->first();
        if(empty($tokens)){  
            return response()->json([
                    "data" => false
                ])->setStatusCode(404);
        }

            if(Carbon::parse($tokens->created_at)->addMinutes(10) > now()  ){
                return response()->json([
                    "data" => true
                ])->setStatusCode(200);
            } else {    
          return response()->json([
                    "data" => false
                ])->setStatusCode(404);
          
            }
        }


    public function PassAuthView($token, Request $request) :JsonResponse {

            $tokenCheck =  PasswordResetToken::where('token' , $token)->first();

            $token = $request->header('Authorization');

            if($token && str_starts_with($token, 'Bearer ')){
                $token = substr($token, 7);
            }

            if(empty($tokenCheck) || $tokenCheck->auth !== "auth" || Carbon::parse($tokenCheck->created_at)->addMinutes(10)->isPast()){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Token Tidak Valid !"
                        ]
                    ]
                   ])->setStatusCode(401));
            }

            $user = Pengguna::where('token' , $token)->first() ?? Administrator::where('token' , $token)->first();
            if(!$user){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Masuk dengan Akun Yang Sesuai"
                        ]
                    ]
                   ])->setStatusCode(404));

            }

            $owner = $tokenCheck->resettable;

            if($owner instanceof Pengguna  && $owner->id_pengguna === $user->id_pengguna){
                return response()->json([
                    "data" => true
                ])->setStatusCode(200);
            }elseif($owner instanceof Administrator  && $owner->id_administrator === $user->id_administrator){
                return response()->json([
                    "data" => true
                ])->setStatusCode(200);
            }
             else {
                return response()->json([
                    "data" => [
                         "data" => false,
                    "message" => "Halaman yang Anda Tuju Tidak Valid!"
                    ]
                ])->setStatusCode(404);
            }
            
          
        }


}
