<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any albums.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view albums
    }

    /**
     * Determine whether the user can vote on the album.
     */
    public function vote(User $user, Album $album): bool
    {
        return true; // Any authenticated user can vote
    }

    /**
     * Determine whether the user can delete the album.
     */
    public function delete(User $user, Album $album): bool
    {
        return $user->isAdmin();
    }
}