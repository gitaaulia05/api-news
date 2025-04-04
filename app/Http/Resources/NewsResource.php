<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'id_administrator' => $this->id_administrator,
            'slug' => $this->slug,
            'judul_berita' => $this->judul_berita,
            'deks_berita' => $this->deks_berita,
            'is_tayang' => $this->is_tayang,
            'created_at' => $this->created_at,
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
            'kategori_berita' => $this->kategori_berita->map(function ($detail) {
                return [
                    'kategori' => $detail->kategori
                ];
            }) , 
            'gambar' => $this->gambar_berita->map(function($detail) {
                return [
                    'gambar_berita' => $detail->gambar_berita,
                    'keterangan_gambar' => $detail->keterangan_gambar
                ];
            })
        ];
    }
}
