<template>
    <div class="thumbnail">
        <div class="thumbnail__mask" @click.prevent="activate"></div>

        <div>
            <img :src="url" alt="thumbnail" class="thumbnail__image">
        </div>

        <thumbnail-menu :photo="photo" :is-active="showThumbnailMenu"
            @delete-photo="deletePhoto"
            @toggle-avatar="toggleAvatar"
            @toggle-visibility="updatePhoto"
            > 
        </thumbnail-menu>
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
             * @return {void}
             */
            deletePhoto() {
                axios.delete('/webapi/albums/' + this.albumSlug + '/photos/' + this.photo.slug)
                    .then(response => {
                        this.$emit('photo-was-deleted', this.photo.id);
                    });
            },

            /**
             * Update photo in database.
             * 
             * @return {void}
             */
            updatePhoto(data) {
                axios.patch('/webapi/albums/' + this.albumSlug + '/photos/' + this.photo.slug, data)
                    .then(response => {;
                        this.updateClientPhoto(response.data.data);
                    });
            },

            /**
             * Toggle avatar property.
             * 
             * @return {void}
             */
            toggleAvatar() {
                axios.patch('/webapi/photos/' + this.photo.slug + '/avatars')
                    .then(response => {;
                        this.$emit('reset-avatars', this.photo.id);
                        this.photo.is_avatar = !this.photo.is_avatar;
                    });  
            },

            /**
             * Update photo on client side.
             * 
             * @param  {object} photo
             * @return {void}      
             */
            updateClientPhoto(photo) {
                for(let prop in photo) {
                    this.photo[prop] = photo[prop];
                }
            }
        },

        mixins: [Thumbnail],

        components: { ThumbnailMenu }
    }
</script>