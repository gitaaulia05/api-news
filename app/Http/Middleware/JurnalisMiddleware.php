<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JurnalisMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $tokenJurnal = $request->header('Authorization');

        if($tokenJurnal && str_starts_with($tokenJurnal , 'Bearer ')){
            $tokenJurnal = substr($tokenJurnal, 7);
        }

        $authenticate = true;

        if(!$tokenJurnal){
            $authenticate = false;
        }

        $jurnalis = Administrator::where('token' , $tokenJurnal)->where('role' , 2)->first();
       
        if(!$jurnalis ||  $jurnalis->role !== 2){
            $authenticate = false;
        } else {
            Auth::guard('administrator')->login($jurnalis);
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
