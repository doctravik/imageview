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

    <form action="/photo" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        
        <input type="file" name="photos[]" multiple>

        <button>Save photo</button>
    </form>

@endsection