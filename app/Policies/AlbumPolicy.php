<?php

namespace App\Policies;

use App\User;
use App\Album;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given album can be viewed by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return bool
     */
    public function show(User $user, Album $album)
    {
        return $user->id === $album->user_id;
    }

    /**
     * Determine if the given album can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return bool
     */
    public function update(User $user, Album $album)
    {
        return $user->isOwnerOf($album);
    }

    /**
     * Determine if the given album can be deleted by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return bool
     */
    public function delete(User $user, Album $album)
    {
        return $user->id === $album->user_id;
    }
}
