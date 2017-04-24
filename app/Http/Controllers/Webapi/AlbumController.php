<?php

namespace App\Http\Controllers\Webapi;

use App\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\AlbumTransformer;
use App\Http\Requests\UpdateAlbumRequest;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AlbumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');    
    }

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

    /**
     * Update album in db.
     * 
     * @param  Album  $album
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAlbumRequest $request, Album $album)
    {
        $this->authorize('update', $album);
        
        $album->update([
            'public' => request('public', false)
        ]);

        return response()->json([], 200);
    }
}
