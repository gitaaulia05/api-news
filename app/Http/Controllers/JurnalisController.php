<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\JurnalisRegisterRequest;

class JurnalisController extends Controller
{
    public function login(AdminLoginRequest $request) : AdminResource{
        $data = $request->validated();

        $jurnalis = Administrator::where('email' , $data['email'])->first();

        if(! $jurnalis || !Hash::check($data['password'] ,  $jurnalis->password)){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Email atau Password Salah!"
                    ]
                ]
            ], 401));
        }
         $jurnalis->token = (String) Str::uuid()->toString();
         $jurnalis->save();

        return (new AdminResource( $jurnalis));
    }


    public function register(JurnalisRegisterRequest $request) : JsonResponse{
        $data = $request->validated();
        $data['id_administrator'] = (String) Str::uuid();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 2;
        $data['active'] = 0;
        
        $jurnalis = new Administrator($data);
        $jurnalis->save();

        return (new AdminResource($jurnalis))->response()->setStatusCode(201);
    }
}
