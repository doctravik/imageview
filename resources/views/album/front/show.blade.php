@extends('layouts.app')
@section('title', $album->name)
@section('content')
    <h1>{{ $album->name }}</h1>

    @can('store', [App\Photo::class, $album])
        @include('album.admin.partials.upload')
    @endcan

    <photos :album="{{ $photos }}" inline-template>
        <div class="columns is-multiline">
            @foreach($photos as $photo)
                <div class="column is-4">
                    <photo 
                        :photo="'{{ $photo->path }}'" 
                        :thumbnail="'{{ $photo->small() }}'"
                        v-on:show-modal="sendAlbum">   
                    </photo>
                </div>
            @endforeach
        </div>
</photos>
@endsection