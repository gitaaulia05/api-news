<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenAdmin = $request->header('Authorization');


        if($tokenAdmin && str_starts_with($tokenAdmin, 'Bearer ')){
            $tokenAdmin = substr($tokenAdmin, 7);
        }
        $authenticate = true;
        
        if(!$tokenAdmin){
            $authenticate = false;
        }

      

        $administrator = Administrator::where('token' , $tokenAdmin)->where('role' , 1)->first();


        if(!$administrator || $administrator->role != 1 ){
            $authenticate = false;
          
        } else {
            Auth::guard('administrator')->login($administrator);
           
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
