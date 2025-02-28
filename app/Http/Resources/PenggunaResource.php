<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'nama' => $this->nama,
            'email' => $this->email,
            // 'alamat' => $this->alamat,
            // 'provinsi' => $this->provinsi,
            // 'kode_pos' => $this->kode_pos,
            // 'pendidikan_terakhir' => $this->pendidikan_terakhir,
            // 'pekerjaan' => $this->pekerjaan,
        ];
        
    }
}
