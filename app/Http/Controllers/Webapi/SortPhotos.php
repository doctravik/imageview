<?php

namespace App\Http\Controllers\Webapi;

use App\Album;
use Illuminate\Http\Request;
use App\Jobs\UpdatePhotosOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\SortPhotosRequest;

class SortPhotos extends Controller
{
    /**
     * Sort photos in album.
     * 
     * @param  Album  $album
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SortPhotosRequest $request, Album $album) 
    {
        $this->authorize('update', $album);

        dispatch(new UpdatePhotosOrder($album, collect(request('photos'))));

        return response()->json([], 200);
    }
}
