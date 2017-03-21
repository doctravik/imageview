<photos :album="{{ $album->photos }}" inline-template>
    <div class="columns is-multiline">
        @foreach($album->photos as $photo)
            <div class="column is-6">
                <photo 
                    :photo="'{{ $photo->path }}'" 
                    :thumbnail="'{{ $photo->small() }}'"
                    v-on:show-modal="sendAlbum">   
                </photo>
            </div>
        @endforeach
    </div>
</photos>