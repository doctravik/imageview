<template>
    <div class="slider_thumbnail" @click="activate"
        :style="{ 'background-image': 'url('+ url +')' }">
    </div>
</template>

<script>
    import Thumbnail from './Thumbnail.vue';

    export default {
        mixins: [Thumbnail]
    }
</script>