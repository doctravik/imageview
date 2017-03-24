<photos :photos="{{ $album->photos }}" inline-template>
    <div>
        <div class="columns is-multiline">
            @foreach($album->photos as $photo)
                <div class="column is-{{ $column_width }}">
                    <photo
                        :photo="{{ $photo }}"
                        :thumbnail="'{{ $photo->small() }}'">   
                    </photo>
                </div>
            @endforeach
        </div>
        <modal :album="{{ $album }}"></modal>
    </div>
</photos>