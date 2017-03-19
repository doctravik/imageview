@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    @if(count($albums))
        <div class="columns">
            <div class="column is-6 is-offset-3">
                @foreach($albums as $album)
                    <ul>
                        <li><a href="{{ $album->url() }}">{{ $album->name }}</a></li>
                    </ul>
                @endforeach
            </div>
        </div>
    @endif

@endsection