@if (count($errors) > 0)
    <div class="notification is-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form class="upload" action="{{ route('albums.photos.store', $album->slug) }}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}

{{--         <label for="uploadPhoto" class="upload__label">
        <span class="upload__header"><b>Drag files here or click to select files</b></span>
    </label> --}}
    <input type="file" name="photos[]" id="uploadPhoto" multiple>
    <button>Save photo</button>
</form>

<hr>