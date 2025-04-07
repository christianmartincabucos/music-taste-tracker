<?php

namespace App\Services;

use App\Models\Album;
use App\Models\User;
use App\Services\Interfaces\AlbumServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AlbumService implements AlbumServiceInterface
{
    public function getAlbums(Request $request): LengthAwarePaginator
    {
        $query = Album::query()->orderByVotesAndName();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('song_name', 'like', "%{$search}%")
                  ->orWhere('artist_name', 'like', "%{$search}%");
            });
        }

        $albums = $query->paginate($request->input('per_page', 10));
        
        // Add user votes if user is authenticated
        if (Auth::check()) {
            $userVotes = Auth::user()->votedAlbums()->pluck('vote', 'album_id');
            
            $albums->getCollection()->transform(function ($album) use ($userVotes) {
                $album->user_vote = $userVotes->get($album->id, 0);
                return $album;
            });
        }
        
        return $albums;
    }

    public function voteOnAlbum(Album $album, int $vote, int $userId): Album
    {
        $user = User::findOrFail($userId);
        
        // Check if user has already voted on this album
        $existingVote = $user->votedAlbums()->wherePivot('album_id', $album->id)->first();

        if ($existingVote) {
            // Update existing vote
            $user->votedAlbums()->updateExistingPivot($album->id, ['vote' => $vote]);
        } else {
            // Create new vote
            $user->votedAlbums()->attach($album->id, ['vote' => $vote]);
        }

        // Get updated album with calculated votes
        $album->refresh();
        $album->user_vote = $vote;
        
        return $album;
    }

    public function deleteAlbum(Album $album): bool
    {
        return $album->delete();
    }
}