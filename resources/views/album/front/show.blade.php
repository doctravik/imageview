@extends('layouts.app')
@section('title', $album->name)
@section('content')
    <h1>{{ $album->name }}</h1>

    @can('store', [App\Photo::class, $album])
        @include('album.admin.partials.upload')
    @endcan

    @include('album.front.partials.photos')
@endsection