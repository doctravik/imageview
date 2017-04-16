<template>
    <div class="thumbnail">
        <div class="thumbnail__mask" @click.prevent="activate"></div>

        <div><img :src="url" alt="thumbnail" class="thumbnail__image"></div>

        <thumbnail-menu :photo="photo" :is-active="showThumbnailMenu"
            @photo-was-deleted="deletePhoto"
            @reset-avatars="resetAvatars"
        ></thumbnail-menu>
    </div>
</template>

<script>
    import Thumbnail from './../mixins/Thumbnail.js';
    import ThumbnailMenu from './ThumbnailMenu.vue';

    export default {
        data() {
            return {
                showThumbnailMenu: false
            }
        },

        methods: {
            deletePhoto(photoId) {
                this.$emit('delete-photo', photoId);
            },

            resetAvatars(photoId) {
                this.$emit('reset-avatars', photoId);
            }
        },

        mixins: [Thumbnail],

        components: { ThumbnailMenu }
    }
</script>