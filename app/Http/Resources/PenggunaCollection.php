<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\PenggunaResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PenggunaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => PenggunaResource::collection($this->collection)
        ];
    }
}
