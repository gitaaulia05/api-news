<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => NewsResource::collection($this->collection)
        ];
    }
}
