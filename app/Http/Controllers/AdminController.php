<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminUpdateRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\JurnalisCollection;
use App\Http\Requests\JurnalisActiveRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminController extends Controller
{
    public function login(AdminLoginRequest $request) : AdminResource{
        $data = $request->validated();
        $admin = Administrator::where('email' , $data['email'])->first();

        if(!$admin || !Hash::check($data['password'] , $admin->password)){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Email atau Password Salah!"
                    ]
                ]
            ], 401));
        }
        $admin->token = (String) Str::uuid()->toString();
        $admin->save();
        return (new AdminResource($admin));
    }

    public function currentAdmin( Request $request) : AdminResource
        {
                $token = $request->bearerToken();
                $admin = Administrator::where('token' , $token)->first();
                //Auth::guard('administrator')->user();
                return new AdminResource($admin);
        }
    public function updateActive(JurnalisActiveRequest $request , $slugJurnalis) : AdminResource{
            $jurnalis = Administrator::where('slug' , $slugJurnalis)->first();
            $jurnalisId =Administrator::where('id_administrator' , $jurnalis->id_administrator)->first();
            
            if($jurnalisId->role == 1)  {
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Aksi Tidak Valid"
                        ]
                    ]
                ])->setStatusCode(404));
            }

            if(!$jurnalisId){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Jurnalis Tidak Ditemukan"
                        ]
                    ]
                ])->setStatusCode(404));
            }

            $data = $request->validated();
            $jurnalisId->fill($data);
            $jurnalisId->save();

            return (new AdminResource($jurnalisId));
    }


    public function updateData(AdminUpdateRequest $request , $slugAdmin) : AdminResource{
        $administratorAuth = Auth::guard('administrator')->user();

        // dd($administratorAuth->slug);
        $adminSlug = Administrator::where('slug' , $slugAdmin)->first();

            $adminId = Administrator::where('id_administrator' , $adminSlug->id_administrator)->first();
           // dd($adminId);
            if(!$adminId || $administratorAuth->slug != $slugAdmin){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Administrator Tidak Ditemukan"
                        ]
                    ]
                ])->setStatusCode(404));
            }
        $data = $request->validated();

            if($request->hasFile('gambar')){
                if($adminId->gambar && Storage::disk('public')->exists($adminId->gambar)){
                    Storage::disk('public')->delete($adminId->gambar);
                }else {
                    $data['gambar'] = $request->file('gambar')->store('profileAdmin' , 'public');
                }
            }
            
        $adminId->fill($data);
        $adminId->save();
        return (new AdminResource($adminId));
    }

    public function searchJurnalis(request $request) : JurnalisCollection{
        $pageJurnal = $request->input('page',1);
        $size = $request->input('size' , 15);

        $jurnalis = Administrator::query()->where('role' , 2);

        $nama = $request->input('nama');
        
        $jurnalis->where(function (Builder $query) use ($nama) {
            if($nama){
                $query->where('role' , 2)->where('nama' , 'like' , '%' . $nama . '%');
            }

        });

        $jurnalis =  $jurnalis->paginate(perPage : $size , page : $pageJurnal);

        return new JurnalisCollection($jurnalis);
    }

    public function showData($slugAdmin) : AdminResource{
        $adminCheck = Administrator::where('slug' , $slugAdmin)->first();
        if(!$adminCheck){
            throw new HttpResponseException(response()->json([
                "errors"=>[
                    "message" => [
                        "Data Administrator Tidak Ditemukan!"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $authAdmin = Auth::guard('administrator')->user();
        $authPengguna = Auth::guard('pengguna')->user();
        $jurnalis = null;
       
       // dd($authPengguna->slug == $adminCheck->slug);
        if ($adminCheck->role == 1 && $authAdmin && $authAdmin->slug == $adminCheck->slug) {
                $jurnalis = Administrator::where('slug' , $slugAdmin)->where('role',1)->first();
                
        } elseif($adminCheck->role == 2 &&  ($authPengguna && $authPengguna->slug == $adminCheck->slug || $authAdmin)) {
                $jurnalis = Administrator::where('slug' , $slugAdmin)->where('role',2)->first();  
        }
       

        if(!$jurnalis){
            throw new HttpResponseException(response()->json([
                "errors"=>[
                    "message" => [
                        "Data Administrator Tidak Ditemukan!"
                    ]
                ]
            ])->setStatusCode(404));
        }
      
        return new AdminResource($jurnalis);
      
    }




    public function logout(Request $request) : JsonResponse {

        $admin = Auth::guard('administrator')->user();
        
        if(!$admin) {
            return response()->json(['errors' => 'Unauthorized'] , 401);
        }

        $admin->token = null;
        $admin->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

}
