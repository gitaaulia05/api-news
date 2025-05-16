<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\kategoriBeritaResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class kategoriBeritaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
        'data' => kategoriBeritaResource::collection($this->collection)
       ];
    }
}
