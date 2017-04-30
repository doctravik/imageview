@extends('layouts.app')
@section('title', 'Edit album')
@section('content')
    <div class="columns">
        <div class="column is-9-mobile is-offset-1-mobile is-6-tablet is-offset-3-tablet is-4-desktop is-offset-4-desktop">
            <nav class="panel">
                <p class="panel-heading">Edit album</p>
                <div class="panel-block">
                    <form class="control" action="{{ route('admin.album.update', $album) }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}

                        <div class="field">
                            <label class="label">Name</label>
                            <p class="control is-expanded">
                                <input class="input" type="text" name="name" placeholder="Name" 
                                    value="{{ old('name', $album->name) }}">
                                <span class="help is-danger">{{ $errors->first('name') }}</span>
                            </p>
                        </div>
    
                        <div class="field is-grouped">
                            <p class="control">
                                <button class="button is-success">Update</button>
                            </p>
                            <p class="control">
                                <a href="{{ url('/admin/albums') }}" class="button">Cancel</a>
                            </p>
                        </div>
                    </form>
                </div>
            </nav>            
        </div>
    </div>
@endsection