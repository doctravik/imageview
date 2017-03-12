@extends('layouts.app')
@section('title', 'Show photo')
@section('content')
    <div class="columns">
        <div class="column is-9 is-offset-3">
            <img src="{{ $photo->url() }}">
        </div>
    </div>
@endsection