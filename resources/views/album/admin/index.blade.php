@extends('layouts.app')
@section('title', 'View albums')
@section('content')
    <div class="columns">
        @if(count($albums))
            <div class="column is-8">
                <nav class="panel">
                    <p class="panel-heading">Albums</p>
                    @foreach($albums as $album) 
                        <div class="panel-block">
                            <div class="control level columns">
                                <div class="column">
                                    <p><a href="{{ $album->url() }}">{{ $album->name }}</a></p>
                                </div>
                                <div class="column has-text-right">
                                    <form action="{{ route('admin.album.destroy', $album->slug) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button class="button info">delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
        @endif
        <div class="column is-4">
            @include('album.admin.partials.create')
        </div>
    </div>
@endsection