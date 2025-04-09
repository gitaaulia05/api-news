<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Symfony\Component\HttpFoundation\Response;

class EmailMiddleware
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

        $user = Pengguna::where('token' , $token)->first() ?? Administrator::where('token' , $token)->first();

        if(!$user) {
            $authenticate = false;
        } else {
            $authenticate = true;
        }
        if($authenticate){
            return $next($request);
        } else {
            return response()->json([
                "errors" => [
                    "message" => [
                        "Tidak Ter-authentikasi"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
