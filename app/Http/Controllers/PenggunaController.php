<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\PenggunaResource;
use App\Http\Requests\PenggunaLoginRequest;
use App\Http\Requests\PenggunaCreateRequest;
use App\Http\Requests\PenggunaUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PenggunaController extends Controller
{
    public function index(PenggunaCreateRequest $request) : JsonResponse{
        $data=$request->validated();
        $data['id_pengguna'] = (String) Str::uuid();
        $data['password'] = Hash::make($data['password']);

        $pengguna = new Pengguna($data);
        $pengguna->save();
        return (new PenggunaResource($pengguna))->response()->setStatusCode(201);
    }

    public function currentPengguna(Request $request) : PenggunaResource{
        $pengguna = Auth::pengguna();
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

            $pengguna->token = Str::uuid()->toString();

            return new PenggunaResource($pengguna);
        
    }

    public function updatePengguna(PenggunaUpdateRequest $request , $slugBuku) : PenggunaResource {
        $pengguna = Pengguna::where('slug' , $slugBuku)->first();
        if(!$buku){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    "message" => [
                        "Pengguna Tidak Ditemukan"
                    ]
                ]
            ])->setStatusCode(404));
        }

    }

    public function logout(Request $request) :JsonResponse {
                $pengguna = Auth::pengguna();
                $pengguna->token = null;

                $pengguna->save();

                return response()->json([
                    "data" => true
                ])->setStatusCode(200);
    }

}
