@extends('layouts.app')
@section('title', $album->name)
@section('content')
    @can('store', [App\Photo::class, $album])
        <div class="columns">
            <div class="column is-12">
                @include('album.admin.partials.upload')
            </div>
        </div>
    @endcan
    
    <div class="columns">
        <div class="column is-12 has-text-centered">
            <h1 class="title is-darkcyan padding-1">
                <b>--- {{ ucfirst($album->name) }} album ---</b>
            </h1>
        </div>
    </div>

    <photos :album="{{ $album }}"></photos>
@endsection