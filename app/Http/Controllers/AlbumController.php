<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlbumVoteRequest;
use App\Models\Album;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\AlbumCollection;
use App\Services\Interfaces\AlbumServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AlbumController extends Controller
{
    protected $albumService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(AlbumServiceInterface $albumService)
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Album::class, 'album', ['except' => ['index']]);
        $this->albumService = $albumService;
    }

    /**
     * Display a paginated listing of albums.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $albums = $this->albumService->getAlbums($request);
        return new AlbumCollection($albums);
    }

    /**
     * Vote on an album.
     *
     * @param Request $request
     * @param Album $album
     * @return JsonResponse
     */
    public function vote(AlbumVoteRequest $request, Album $album): JsonResponse
    {
        $album = $this->albumService->voteOnAlbum(
            $album, 
            $request->validated('vote'), 
            auth()->id()
        );

        return response()->json([
            'data' => new AlbumResource($album),
            'message' => 'Vote recorded successfully'
        ]);
    }

    /**
     * Remove the specified album from storage.
     *
     * @param Album $album
     * @return JsonResponse
     */
    public function destroy(Album $album): JsonResponse
    {
        $this->albumService->deleteAlbum($album);

        return response()->json([
            'message' => 'Album deleted successfully'
        ], 200);
    }
}