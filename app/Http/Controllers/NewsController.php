<?php

namespace App\Http\Controllers;

use session;
use Carbon\Carbon;
use App\Models\berita;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\gambar_berita;
use App\Models\kategori_berita;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NewsCollection;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\NewsCreateRequest;
use App\Http\Requests\NewsUpdateRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewsController extends Controller
{

    public function index(Request $request) : NewsCollection
    {  
        // dd( Carbon::today()->toDateString());
        $pageNews= $request->input('page', 1);
        $size = $request->input('size' , 15);

        $Pengguna = Auth::guard('administrator')->user();

        $newsQuery = berita::withTrashed();

        if($Pengguna->role == 2) {
            $newsQuery = $newsQuery->where('id_administrator' , $Pengguna->id_administrator);
        }

        $nama = $request->input('judul_berita');
        $is_tayang = $request->input('is_tayang');
        $is_trash = $request->input('is_trash');
     
        $newsQuery->where(function (Builder $query) use ($nama , $is_tayang , $is_trash) {
          
                if($nama) {
                    $query->where('judul_berita' , 'like' , '%'.$nama.'%');
                }

                if($is_tayang !== NULL) {
                    $query->where('is_tayang' , $is_tayang);
                }
                if($is_trash !== NULL) {
                    $query->where('is_tayang' , $is_trash);
                }
        });

        $news = $newsQuery->paginate(perPage : $size , page: $pageNews);
        return new NewsCollection($news);
    }

    public function allNews(Request $request):  NewsCollection | JsonResponse {

        // dd( Carbon::today()->toDateString());
        $pageNews= $request->input('page', 1);
        $size = $request->input('size' , 15);

        $Pengguna = Auth::guard('administrator')->user();

        $newsQuery = berita::withTrashed();


        $nama = $request->input('judul_berita');
        $newest = $request->input('newest');
        $weekNews = $request->input('weekNews');

        $newsQuery->where(function (Builder $query) use ($nama , $newest, $weekNews) {
          
                if($nama) {
                    $query->where('judul_berita' , 'like' , '%'.$nama.'%');
                }

                if($newest !== NULL) {
                       
                    $hasTodayNews = berita::withTrashed()->where('updated_at', 'like' , '%'. $newest . '%')->exists();
                  
                        if(!$hasTodayNews){
                            $yesterdayNews = Carbon::parse($newest)->subDay()->toDateString();
                            dd($yesterdayNews);
                            $query->where('updated_at', 'like' , '%'. $yesterdayNews . '%');
                        } else {
                            $query->where('updated_at', 'like' , '%'. $newest . '%');
                        }
                }

                if($weekNews !== NULL) {
                    dd('hahaha');
                }
        });
        $news = $newsQuery->paginate(perPage : $size , page: $pageNews );
       
        $etag = md5(json_encode(
            [
                'data' => $news,
                'updated_at' => $newsQuery->max('updated_at'),
            ]
        ));

        if($request->header('If-None-Match') === $etag){
            return response()->json(null, 304);
        }
      return (new NewsCollection($news))->response()->header('ETag', $etag);

    }
    
    public function storeNews(NewsCreateRequest $request): JsonResponse {
        $data = $request->validated();
        $data['id_berita'] = (String) Str::uuid();
        $data['id_administrator'] = Auth::guard('administrator')->id();

       $news = new berita($data);

        $kategoriBerita = new kategori_berita([
            'id_kategori_berita' => (string) Str::uuid(),
            'id_berita' =>  $news->id_berita,
            'kategori' => $request->kategori
        ]);

        $gambarFields = [
            ['file' => 'gambar' , 'keterangan' =>'keterangan_gambar' , 'posisi_gambar' => 'gambar utama' ],
            ['file' => 'gambar2' , 'keterangan' =>'keterangan_gambar2',  'posisi_gambar' => 'gambar tambahan']      
    ];

    foreach($gambarFields as $gf){
        if($request->hasFile($gf['file'])) {
            $gambarPath = $request->file($gf['file'])->store('gambarNews' , 'public');

        $gambar_berita = new gambar_berita([
         'id_gambar' => (String) Str::uuid(),
         'id_berita' =>  $news->id_berita,
         'gambar_berita' => $gambarPath,
         'keterangan_gambar' => $request->{$gf['keterangan']},
         'posisi_gambar' => $gf['posisi_gambar']
        ]);
        $news->save();
        $kategoriBerita->save();
        $gambar_berita->save();
        }
    }
       
    return (new NewsResource($news))->response()->setStatusCode(201);
}


public function updateNews(NewsUpdateRequest $request, $slugBerita): NewsResource
{
    
    $berita = berita::where('slug', $slugBerita)->first();

    if (!$berita) {
        throw new HttpResponseException(response()->json([
            'errors' => ['message' => ['Berita tidak ditemukan']]
        ], 404));
    }
    
    $data = $request->validated();

    // Update berita
    $berita->update([
        'judul_berita' => $data['judul_berita'],
        'deks_berita' => $data['deks_berita'],
    ]);

    // Update kategori berita
    kategori_berita::where('id_berita', $berita->id_berita)->update([
        'kategori' => $data['kategori']
    ]);

    // **Cek dan Update Gambar**
    $gambarFields = [
        ['file' => 'gambar', 'keterangan' => 'keterangan_gambar', 'posisi_gambar' => 'gambar utama'],
        ['file' => 'gambar2', 'keterangan' => 'keterangan_gambar2', 'posisi_gambar' => 'gambar opsional']
    ];

    $gambarBeritaList = gambar_berita::where('id_berita', $berita->id_berita)
    ->whereIn('posisi_gambar', ['gambar utama', 'gambar opsional'])
    ->first();

    foreach ($gambarFields as $gf) {
        if ($request->hasFile($gf['file'])) {
            $gambarBerita= $gambarBeritaList->where('posisi_gambar', $gf['posisi_gambar'])->first();
            // Jika ada gambar sebelumnya, hapus file lama
            if ($gambarBerita) {
                if (!empty($gambarBerita->gambar_berita) && Storage::disk('public')->exists($gambarBerita->gambar_berita)) {
                    Storage::disk('public')->delete($gambarBerita->gambar_berita);
                }

                // Simpan gambar baru
                $gambarPath = $request->file($gf['file'])->store('gambarNews', 'public');

                // Update data gambar
                $gambarBerita->update([
                    'gambar_berita' => $gambarPath,
                    'keterangan_gambar' => $data[$gf['keterangan']] ?? 'Deskripsi default'
                ]);

           
            } else {
                // Jika tidak ada gambar sebelumnya, buat data baru
                $gambarPath = $request->file($gf['file'])->store('gambarNews', 'public');

                gambar_berita::create([
                    'id_gambar' => (String) Str::uuid(),
                    'id_berita' => $berita->id_berita,
                    'gambar_berita' => $gambarPath,
                    'keterangan_gambar' => $data[$gf['keterangan']] ?? 'Deskripsi default',
                    'posisi_gambar' => $gf['posisi_gambar']
                ]);
            }
        } 
    }

    gambar_berita::where('id_berita' , $berita->id_berita)->update([
        'slug' => Str::slug($berita->judul_berita)
    ]);
    return new NewsResource($berita);
}

    public function showNews($slugBerita) : NewsResource
    {
        
        $admin = Auth::guard('administrator')->user();

        $query = berita::withTrashed("slug" , $slugBerita)->first();
        if($admin->role == 2){
           $query->where('id_administrator' , $admin->id_administrator);
        } 

        $news = $query->first();
        if(!$news){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Berita Tidak Ditemukan"
                    ]
                ]
            ], 401));
        }

        return new NewsResource($news);
    }

    public function softDelete($slugBerita) : JsonResponse
    {
        $jurnalis = Auth::guard('administrator')->user();
      
        $news = berita::withTrashed()->where("slug" , $slugBerita)->first();
        // ->where('id_administrator' , $jurnalis->id_administrator )-
        if(!$news){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Berita Tidak Ditemukan". $news   , 
                        
                    ]
                ]
            ], 401));
        }

        $news->update([
            'is_tayang' => '2'
        ]);

        $news->delete();

        return response()->json([
            "data" => [
                'message' => 'Berita Berhasil Dihapus, Dapat di recovery sebelum 30 hari'
            ]
        ])->setStatusCode(200);

    }   


    public function trashBin() : NewsCollection {
        $jurnalis = Auth::guard('administrator')->user;
        $news = berita::onlyTrashed()->where('id_administrator' , $jurnalis->id_administrator)->get();
            
        return new NewsCollection($news);
    }

    public function restoreNews($slugBerita) : JsonResponse {
        $jurnalis = Auth::guard('administrator')->user();

        $news = berita::onlyTrashed()->where('slug' , $slugBerita)->where('id_administrator' , $jurnalis->id_administrator)->first();
        
        if(!$news){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Berita Tidak Ditemukan"
                    ]
                ]
            ], 401));
        }

        $news->update([
            'is_tayang' => '1'
        ]);
            $news->restore();

            return response()->json([
                "data" => [
                    'message' => 'Berita Berhasil di Recovery'
                ]
            ])->setStatusCode(200);
    }

    public function delete($slugBerita) : JsonResponse {
        $jurnalis = Auth::guard('administrator')->user();
        $berita = berita::onlyTrashed()->where('slug' , $slugBerita)->where('id_administrator' , $jurnalis->id_administrator)->first();

        if(!$berita) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Berita Tidak Ditemukan"
                    ]
                ]
            ], 401));
        }

        $berita->forceDelete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);

    }

    public function counter($kategori , berita $slugBerita): NewsResource{
        if(!$slugBerita) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Berita Tidak Ditemukan"
                    ]
                ]
            ], 404));
        }
        $sessionId = session()->getId();
        $slugBerita->visit()->withSession($sessionId);
        return new NewsResource($slugBerita);
    }

    public function showNew($slugBerita) : NewsResource
    {
        
        $admin = Auth::guard('administrator')->user();

        $query = berita::withTrashed("slug" , $slugBerita)->first();
        if($admin->role == 2){
           $query->where('id_administrator' , $admin->id_administrator);
        } 

        $news = $query->first();
        if(!$news){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Berita Tidak Ditemukan"
                    ]
                ]
            ], 401));
        }

        return new NewsResource($news);
    }

    public function popularNews() : NewsCollection{
        $berita = berita::popularThisWeek()->get();
        return new NewsCollection($berita);
    }

    public function relatedNews() : NewsCollection{
        
    }
   
}
