<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\AdminResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JurnalisCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
            'data' => AdminResource::collection($this->collection),
        ];
    }
}
