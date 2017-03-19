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
     * Delete album from database.
     * 
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        $album->delete();

        return redirect('/admin/albums');
    }
}
