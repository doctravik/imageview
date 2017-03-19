@extends('layouts.app')
@section('title', $album->name)
@section('content')
    <h1>{{ $album->name }}</h1>

    {{-- @can('') --}}
        @include('album.admin.partials.upload')
    {{-- @endcan --}}

    <div class="columns is-multiline">
        @foreach($photos as $photo)
            <div class="column is-4 has-text-centered">
                <a href="{{ route('photos.show', $photo->slug) }}">
                    <img src="{{ $photo->small() }}" alt="{{ $album->name }} photo">
                </a>
            </div>
        @endforeach
    </div>
@endsection