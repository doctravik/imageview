@if (count($errors) > 0)
    <div class="notification is-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<upload-form :album="{{ $album }}"></upload-form>
{{-- <form class="upload" action="{{ route('albums.photos.store', $album->slug) }}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}

    <input type="file" name="photos[]" id="uploadPhoto" multiple>
    <button>Save photo</button>
</form> --}}

<hr>