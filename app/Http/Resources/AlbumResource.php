<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'song_name' => $this->song_name,
            'cover_image' => $this->cover_image,
            'artist_name' => $this->artist_name,
            'album_cover' => $this->album_cover,
            'release_date' => $this->release_date,
            'genre' => $this->genre,
            'total_votes' => $this->total_votes,
            'user_vote' => $this->when(auth()->check(), $this->user_vote ?? 0),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}