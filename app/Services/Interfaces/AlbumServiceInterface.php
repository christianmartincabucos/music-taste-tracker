<?php

namespace App\Services\Interfaces;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface AlbumServiceInterface
{
    public function getAlbums(Request $request): LengthAwarePaginator;
    public function voteOnAlbum(Album $album, int $vote, int $userId): Album;
    public function deleteAlbum(Album $album): bool;
}