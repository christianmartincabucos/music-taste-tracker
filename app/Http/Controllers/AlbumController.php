<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    /**
     * Display a listing of the albums.
     */
    public function index(Request $request)
    {
        $query = Album::query()->orderByVotesAndName();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('song_name', 'like', "%{$search}%")
                  ->orWhere('artist_name', 'like', "%{$search}%");
            });
        }

        // Pagination
        $albums = $query->paginate(10);

        // Add user's vote to each album
        $user = Auth::user();
        if ($user) {
            $userVotes = $user->votedAlbums()->pluck('vote', 'album_id');
            
            $albums->getCollection()->transform(function ($album) use ($userVotes) {
                $album->user_vote = $userVotes->get($album->id, 0);
                return $album;
            });
        } 

        return response()->json($albums);
    }

    /**
     * Vote on an album.
     */
    public function vote(Request $request, Album $album)
    {
        $request->validate([
            'vote' => 'required|integer|in:-1,1',
        ]);

        $user = Auth::user();
        $vote = $request->input('vote');

        // Check if user has already voted on this album
        $existingVote = $album->voters()->where('user_id', $user->id)->first();

        if ($existingVote) {
            // Update existing vote
            $album->voters()->updateExistingPivot($user->id, ['vote' => $vote]);
        } else {
            // Create new vote
            $album->voters()->attach($user->id, ['vote' => $vote]);
        }

        // Get updated album with total votes
        $album->refresh();
        $album->user_vote = $vote;

        return response()->json($album);
    }

    /**
     * Remove the specified album from storage.
     */
    public function destroy(Album $album)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $album->delete();

        return response()->json(['message' => 'Album deleted successfully']);
    }
}

