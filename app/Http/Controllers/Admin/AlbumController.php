<?php

namespace App\Http\Controllers\Admin;

use App\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;

class AlbumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show all albums of the user.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = auth()->user()->albums;

        return view('album.admin.index', compact('albums'));
    }

    /**
     * Store album in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlbumRequest $request)
    {
        auth()->user()->albums()->create([
            'name' => request('name')
        ]);

        return redirect('/admin/albums');
    }

    /**
     * Update album in db.
     *
     * @param  UpdateAlbumRequest $request
     * @param  Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlbumRequest $request, Album $album)
    {
        $this->authorize('update', $album);
        
        $album->update([
            'public' => (bool) request('public', false)
        ]);

        return redirect('/admin/albums');
    }

    /**
     * Show photos from album.
     * 
     * @param  string $albumSlug
     * @return \Illuminate\Http\Response
     */
    public function show($albumSlug)
    {
        $album = auth()->user()->findAlbumBySlug($albumSlug);

        $this->authorize('show', $album);

        return view('album.admin.show', compact('album'));
    }

    /**
     * Delete album from database.
     * 
     * @param  string  $albumSlug
     * @return \Illuminate\Http\Response
     */
    public function destroy($albumSlug)
    {
        $album = auth()->user()->findAlbumBySlug($albumSlug);

        $this->authorize('delete', $album);
        
        $album->delete();

        return redirect('/admin/albums');
    }
}
