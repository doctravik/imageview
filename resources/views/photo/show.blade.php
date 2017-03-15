@extends('layouts.app')
@section('title', 'Show photo')
@section('content')
    <div class="columns">
        <div class="column">
            @if($prev)
                <a href="{{ route('photo.show', ['id' => $prev->id]) }}" class="photo-view__control has-text-centered">
                    <i class="fa fa-caret-left" aria-hidden="true"></i>
                </a>        
            @endif
        </div>

        <div class="column is-10 has-text-centered">
            <img src="{{ $photo->url() }}">
        </div>

        <div class="column has-text-centered">
            @if($next)
                <a href="{{ route('photo.show', ['id' => $next->id]) }}" class="photo-view__control has-text-centered">
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                </a>
            @endif
        </div>
    </div>
@endsection