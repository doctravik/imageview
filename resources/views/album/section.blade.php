<div class="columns is-multiline">
    @foreach($album->photos as $photo)
        <div class="column is-6">
            <a href="{{ route('photos.show', $photo->slug) }}">
                <img src="{{ $photo->small() }}" alt="photo">
            </a>
        </div>
    @endforeach
</div>