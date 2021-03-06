<?php

namespace App\Traits;

use App\Album;

trait InteractWithAlbum
{
    /**
     * Has many albums relation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany(Album::class);    
    }

    /**
     * Add album.
     *
     * @param Album $album
     * @return Album
     */
    public function addAlbum(Album $album)
    {
        return $this->albums()->save($album);    
    }

    /**
     * Check if it has the given album.
     * 
     * @param  Album   $album
     * @return boolean
     */
    public function hasAlbum(Album $album)
    {
        return $this->albums->contains(function($value) use ($album) {
            return $value->id === $album->id;
        });
    }

    /**
     * Check whether user has album.
     * 
     * @return boolean
     */
    public function hasAlbums()
    {
        return (bool) $this->albums->count();
    }
}