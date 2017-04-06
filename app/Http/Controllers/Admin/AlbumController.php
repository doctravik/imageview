<?php

namespace App\Http\Controllers\Admin;

use App\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:albums,name'
        ]);

        auth()->user()->albums()->create([
            'name' => request('name')
        ]);

        return redirect('/admin/albums');
    }

    /**
     * Show photos from album.
     * 
     * @param  Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {        
        return view('album.admin.show', compact('album'));
    }

    /**
     * Delete album from database.
     * 
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);
        
        $album->delete();

        return redirect('/admin/albums');
    }
}
