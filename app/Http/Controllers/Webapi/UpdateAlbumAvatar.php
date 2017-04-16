<?php

namespace App\Http\Controllers\Webapi;

use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UpdateAlbumAvatar extends Controller
{
    /**
     * Update avatar property of the photo.
     * 
     * @param  Photo  $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Photo $photo)
    {
        $this->authorize('update', [Photo::class, $album = $photo->album]);

        $album->resetAvatars();

        $photo->toggleAvatar();

        return response()->json([], 200);
    }
}
