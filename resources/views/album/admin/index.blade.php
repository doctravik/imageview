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
                            <div class="control level is-mobile">
                                <div class="level-left">
                                    <p class="level-item"><a href="{{ $album->url() }}">{{ $album->name }}</a></p>
                                </div>
                                <div class="level-right">
                                    <div class="level-item">
                                        <form action="{{ route('admin.album.update', $album) }}" method="POST" >
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <div class="field">
                                                <p class="control">
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="public" {{ $album->isPublic() ? 'checked' : '' }} onchange="this.form.submit()">public
                                                    </label>
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="level-item">
                                        <form action="{{ route('admin.album.destroy', $album->slug) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button class="button info">delete</button>
                                        </form>
                                    </div>
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