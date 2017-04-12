<template>
    <div class="thumbnail">
        <div class="thumbnail__mask" @click.prevent="activate"></div>

        <div>
            <img :src="url" alt="thumbnail" class="thumbnail__image">
        </div>

        <thumbnail-menu @delete-photo="deletePhoto" :is-active="showThumbnailMenu"></thumbnail-menu>
    </div>
</template>

<script>
    import Thumbnail from './../mixins/Thumbnail.js';
    import ThumbnailMenu from './ThumbnailMenu.vue';

    export default {
        props: ['albumSlug'],

        data() {
            return {
                showThumbnailMenu: false
            }
        },

        methods: {
            /**
             * Delete photo from database.
             * 
             * @return {promise}
             */
            deletePhoto() {
                axios.delete('/webapi/albums/' + this.albumSlug + '/photos/' + this.photo.slug)
                    .then(response => {
                        this.$emit('photo-was-deleted', this.photo.id);
                    });
            }
        },

        mixins: [Thumbnail],

        components: { ThumbnailMenu }
    }
</script>