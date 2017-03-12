@extends('layouts.app')
@section('title', 'Welcome')

@section('content')
    <div class="columns is-multiline">
        @foreach($users as $user)
            <div class="column is-6">
                @include('user.front.album')
            </div>
        @endforeach
    </div>
@endsection