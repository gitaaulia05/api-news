<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class PenggunaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if($token && str_starts_with($token, 'Bearer ')){
            $token = substr($token, 7);
        }

        $authenticate = true;

        if(!$token) {
            $authenticate = false;
        }

        $pengguna = Pengguna::where('token' , $token)->first();


        if(!$pengguna) {
            $authenticate = false;
        } else {
            Auth::guard('pengguna')->login($pengguna);
        }

        if($authenticate){
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => [
                        "Tidak ter-authentikasi" 
                    ]
                ]
            ])->setStatusCode(401);
        }
    
    }
}
