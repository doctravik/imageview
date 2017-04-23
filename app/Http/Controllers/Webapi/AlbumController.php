<?php

namespace App\Http\Controllers\Webapi;

use App\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\AlbumTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AlbumController extends Controller
{
    /**
     * Get albums for the welcome page.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $albums = Album::latest()->paginate(9);

        return fractal()
            ->collection($albums->getCollection())
            ->transformWith(new AlbumTransformer)
            ->parseIncludes(['user', 'avatar', 'publicPhotos'])
            ->paginateWith(new IlluminatePaginatorAdapter($albums))
            ->toArray();
    }
}
