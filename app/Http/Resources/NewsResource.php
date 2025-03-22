<?php

namespace App\Http\Resources;

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
