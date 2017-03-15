@extends('layouts.app')
@section('title', 'Welcome')

@section('content')
    <div class="columns is-multiline">
        @foreach($albums as $album)
            <div class="column is-6">
                @include('album.section')
            </div>
        @endforeach
    </div>
@endsection