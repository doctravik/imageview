<photos :album="{{ $album }}" :photos="{{ $photos }}"></photos>
{{-- <photos :photos="{{ $photos }}" inline-template>
    <div>
        <div class="columns is-multiline">
            @foreach($photos as $photo)
                <div class="column is-4">
                    <photo
                        :photo="{{ $photo }}"
                        :thumbnail="'{{ $photo->small() }}'">
                    </photo>
                </div>
            @endforeach
        </div>
        <modal :album="{{ $album }}"></modal>
    </div>
</photos> --}}