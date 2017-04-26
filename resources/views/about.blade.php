@extends('layouts.app')
@section('title', 'About')
@section('content')
    <div class="has-text-centered">
        <div class="is-inline-block">
            <div class="content box has-text-left">
                <h3>It's educational project.</h3>
                <p><b>Site has client and admin sections.</b></p>
                <p><b>Authenticated user can:</b></p>
                <ul>
                    <li>create albums</li>
                    <li>upload images to the album</li>
                    <li>set image and album as private or public</li>
                    <li>set avatar for the album</li>
                    <li>delete own images</li>
                </ul>
                <p><b>Guest can only view public albums with public photos.</b></p>
            </div>
        </div>
    </div>
@endsection