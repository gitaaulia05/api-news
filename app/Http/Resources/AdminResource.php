<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'nama' => $this->nama,
            'email' => $this->email,
            'slug' => $this->slug,
            'gambar' => $this->gambar,
            'token' => $this->token,
            'role' => $this->role,
            'active' => $this->active,
        ];

        
    }
}
