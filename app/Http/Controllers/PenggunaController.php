<?php

namespace App\Http\Controllers;

use Log;
use App\Mail\NewsApi;
use App\Models\Pengguna;
use App\Mail\NewsApiAuth;
use Illuminate\Support\Str;
use App\Models\Administrator;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\PasswordResetToken;
use App\Services\PasswordServices;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EmailRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
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

        $data = $request->validated();
        $pengguna->fill($data);
        $pengguna->save();
            return ( new PenggunaResource($pengguna));
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

    \Log::info('Masuk ke sendEmailAuth', ['request_data' => $request->all()]);

     $pengguna = Auth::guard('pengguna')->user();
     $administrator = Auth::guard('administrator')->user();

    $user = $pengguna ?? $administrator;
    \Log::info($user);
         if(!$user) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Anda Belum Login Silahkan Login Terlebih Dahulu !'
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

            if($pengguna->email !== $data['email']){
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
                    'resettable_id' => $pengguna->getKey(),
                    'resettable_type' => get_class($pengguna),
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
                Mail::to($pengguna->email)->send(new NewsApiAuth($msg, $subject));
            return  response()->json([
                "data" => [
                    "message" =>  "Email Berhasil dikirim Ke email anda" ,
                    "email" => $pengguna->email,
                    "message2" => "Periksa Email Anda Untuk Reset Password"
                ]
            ])->setStatusCode(200);
}



// store without auth
    public function forgetPassword(PenggunaForgetPassRequest $request , $token) : JsonResponse{
        $password= PasswordResetToken::where('token' , $token)->first();
      
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

// Store with Auth
    public function storeNewPassword(PenggunaForgetPassRequest $request , $token) : JsonResponse {
        \Log::info('masu');
     $pengguna = Auth::guard('pengguna')->user();
\Log::info($pengguna. 'nih');
        $password= PasswordResetToken::where('token' , $token)->first();
      
        if(!$password || !$pengguna){
           throw new HttpResponseException(response()->json([
            "errors" => [
                "message" => [
                    "Halaman Tidak Valid, Kirim Kembali Kode Ke Email Anda!"
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


public function PassAuthView($token) :JsonResponse {

            $tokenCheck =  PasswordResetToken::where('token' , $token)->first();
            $pengguna = Auth::guard('pengguna')->user();
            if(!$pengguna){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Masuk dengan Akun Yang Sesuai"
                        ]
                    ]
                   ])->setStatusCode(404));

            }

            $owner = $tokenCheck->resettable;
            if($owner instanceof Pengguna && $pengguna && $owner->id_pengguna === $pengguna->id_pengguna){
                return response()->json([
                    "data" => true
                ])->setStatusCode(200);
            } else {
                return response()->json([
                    "data" => [
                         "data" => false,
                    "message" => "Halaman yang Anda Tuju Tidak Valid!"
                    ]
                ])->setStatusCode(404);
            }
            
            if(empty($tokenCheck) || $tokenCheck->auth !== "auth" || Carbon::parse($tokenCheck->created_at)->addMinutes(10)){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Token Tidak Valid !"
                        ]
                    ]
                   ])->setStatusCode(404));
            }
        }


}
