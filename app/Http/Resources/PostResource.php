<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         =>  $this->id,
            'title'      =>  $this->title,
            'description'=>  $this->description,
            'image'      =>  $this->image ? url(Storage::url($this->image)) : null,
            'category'   =>  CategoryResource::make($this->whenLoaded('category')),
            'author'     =>  UserResource::make($this->whenLoaded('user')), //single user,that's why we use "make"
            'tags'       =>  TagResource::collection($this->whenLoaded('tags')), // might have many tags, that's why it's a collection!
            'published'  =>  $this->created_at->format('Y-m-d'),
        ];
    }
}
