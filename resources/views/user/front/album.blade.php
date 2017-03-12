<div class="columns is-multiline">
    @foreach($user->photos as $photo)
        <div class="column is-6">
            <a href="{{ route('photo.show', ['id' => $photo->id]) }}">
                <img src="{{ $photo->url() }}" alt="photo">
            </a>
        </div>
    @endforeach
</div>