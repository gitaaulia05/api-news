<?php

namespace App\Http\Controllers;

use App\Models\berita;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\NewsCreateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewsController extends Controller
{

    public function index(Request $request) : NewsCollection
    {

        $pageNews= $request->input('page', 1);
        $size = $request->input('size' , 25);

        $news = berita::query();

        $nama = $request->input('judul_berita');
        $is_tayang = $request->input('is_tayang');

        $news->where(function (Builder $query) use ($nama , $is_tayang) {
                if($nama) {
                    $query->where('judul_berita' , 'like' , '%'.$nama.'%');
                }

                if($is_tayang !== null) {
                    $query->where('is_tayang' , 'like' , '%'.$is_tayang.'%');
                }
        });

        $news = $news->paginate(perPage : $size , page: $pageNews);
        return new NewsCollection($buku);
    }
    
    public function storeNews(NewsCreateRequest $request): jsonResponse {
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


public function updateNews(NewsCreateRequest $request, $slugBerita): NewsResource
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


}
