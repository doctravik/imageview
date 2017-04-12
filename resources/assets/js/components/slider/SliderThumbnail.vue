<template>
    <div class="slider_thumbnail" @click="activate"
        :style="{ 'background-image': 'url('+ url +')' }">
    </div>
</template>

<script>
    import Thumbnail from './../mixins/Thumbnail.js';

    export default {
        mixins: [Thumbnail]
    }
</script>