<?php

namespace App\Transformers;

use App\User;
use App\Album;
use App\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class AlbumTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = ['user'];

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
    public function includeUser(User $user)
    {
        return $this->item($album->user, new UserTransformer);
    }
}