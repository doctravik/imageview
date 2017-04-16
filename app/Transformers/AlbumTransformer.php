<?php

namespace App\Transformers;

use App\User;
use App\Album;
use App\Transformers\UserTransformer;
use App\Transformers\PhotoTransformer;
use League\Fractal\TransformerAbstract;

class AlbumTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = ['user', 'publicPhotos', 'avatar'];

    /**
     * Turn this Album object into a generic array.
     *
     * @return array
     */
    public function transform(Album $album)
    {
        return [
            'id' => (int) $album->id,
            'name' => $album->name,
            'slug' => $album->slug
        ];
    }

    /**
     * Include User
     *
     * @return League\Fractal\ItemResource
     */
    public function includeUser(Album $album)
    {
        return $this->item($album->user, new UserTransformer);
    }

    /**
     * Include User
     *
     * @return League\Fractal\ItemResource
     */
    public function includeAvatar(Album $album)
    {
        $avatar = $album->avatar;

        if($avatar === null) {
            return null;
        }

        return $this->item($album->avatar, new PhotoTransformer);
    }

    /**
     * Include User
     *
     * @return League\Fractal\ItemResource
     */
    public function includePublicPhotos(Album $album)
    {
        // dd($album->publicPhotos);
        return $this->collection($album->publicPhotos, new PhotoTransformer);
    }
}