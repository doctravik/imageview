<?php

namespace App\Transformers;

use App\Photo;
use App\Transformers\AlbumTransformer;
use League\Fractal\TransformerAbstract;

class PhotoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = ['album'];

    /**
     * Turn this Photo object into a generic array.
     *
     * @return array
     */
    public function transform(Photo $photo)
    {
        return [
            'id' => (int) $photo->id,
            'name' => $photo->name,
            'slug' => $photo->slug,
            'path' => $photo->path,
            'link' => $photo->link,
            'description' => $photo->description,
        ];
    }

    /**
     * Include Album
     *
     * @return League\Fractal\ItemResource
     */
    public function includeAlbum(Photo $photo)
    {
        return $this->item($photo->album, new AlbumTransformer);
    }
}