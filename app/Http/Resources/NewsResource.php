<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\simpanBerita;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      
        return [
            'id_administrator' => $this->berita->id_administrator ??  $this->id_administrator,
            'slug' => $this->berita->slug ??  $this->slug,
            'nama_jurnalis' => $this->administrator->nama ??  $this->nama,
            'id_berita' => $this->berita->id_berita ?? $this->id_berita,
            'slug' => $this->berita->slug ??$this->slug ,
            'judul_berita' => $this->berita->judul_berita ?? $this->judul_berita,
            'deks_berita' => $this->berita->deks_berita ?? $this->deks_berita,
            'is_tayang' => $this->berita->is_tayang ?? $this->is_tayang,
            'created_at' => $this->berita->created_at ?? $this->created_at,
            'simpanBerita' =>  $this->simpanBerita && $this->simpanBerita->isNotEmpty(),
            'updated_at' => Carbon::parse($this->berita->updated_at ??$this->updated_at)->diffForHumans(),
           'kategori_berita' => $this->kategori_berita?->kategori 
                     ?? $this->berita?->kategori_berita?->kategori,
            'gambar' =>($this->berita->gambar_berita ?? $this->gambar_berita)?->map(function($detail) {
                return [
                    'gambar_berita' => $detail->gambar_berita,
                    'keterangan_gambar' => $detail->keterangan_gambar
                ];
            })
        ];
    }
}
