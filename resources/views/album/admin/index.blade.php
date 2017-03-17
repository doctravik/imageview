@extends('layouts.app')
@section('title', 'View albums')
@section('content')
    @if(count($albums))
        <div class="columns">
            <div class="column is-6">
                @foreach($albums as $album)
                    <div class="media">
                        <div class="media-content"><p><a href="{{ $album->url() }}">{{ $album->name }}</a></p></div>
                        <div class="media-right has-text-right">
                            <form class="control" action="{{ $album->url() }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button class="button info">delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="column is-1"></div>
            <div class="column is-5">
                <form action="{{ url('/admin/albums') }}" method="POST" class="control notification box">
                    {{ csrf_field() }}
                    <div class="field">
                        <label class="label">Name</label>
                        <p class="control">
                            <input class="input" name="name" type="text" placeholder="Name" value="{{ old('name') }}">
                        </p>
                        @if($errors->has('name'))
                            <p class="help is-danger">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="field">
                        <p class="control">
                            <button class="button is-primary">Create album</button>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection