@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    @if (count($errors) > 0)
        <div class="notification is-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="upload" action="/photo" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

{{--         <label for="uploadPhoto" class="upload__label">
            <span class="upload__header"><b>Drag files here or click to select files</b></span>
        </label> --}}
        <input type="file" name="photos[]" id="uploadPhoto" multiple>
        <button>Save photo</button>
    </form>

    <hr>

    @if(count($album))
        <div class="columns">
            @foreach($album->photos as $photo)
                <div class="column is-3">
                    <img src="{{ $photo->url() }}" alt="photo">
                </div>
            @endforeach
        </div>
    @endif

@endsection