<?php

namespace App\Policies;

use App\User;
use App\Album;
use App\Photo;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can get all photo from album.
     *
     * @param  \App\User  $user
     * @param  \App\Album $album
     * @return bool
     */
    public function index(User $user, Album $album)
    {
        return $user->id === $album->user_id;
    }

    /**
     * Determine if the user can store photo to the given album.
     *
     * @param  \App\User  $user
     * @param  \App\Album $album
     * @return bool
     */
    public function store(User $user, Album $album)
    {
        return $user->id === $album->user_id;
    }

    /**
     * Determine if the user can update photo of the given album.
     *
     * @param  \App\User  $user
     * @param  \App\Album $album
     * @return bool
     */
    public function update(User $user, Album $album)
    {
        return $user->isOwnerOf($album);
    }

    /**
     * Determine if the user can delete photo.
     *
     * @param  \App\User  $user
     * @param  \App\Photo $photo
     * @return bool
     */
    public function destroy(User $user, Photo $photo, Album $album)
    {
        return $user->isOwnerOf($album);
    }
}
