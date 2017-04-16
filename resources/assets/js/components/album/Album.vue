<template>
    <album-thumbnail size="small" @activate-thumbnail="showModal"
        :album="album"
        :photo="avatar"
        :user="username"
        :count="count">
    </album-thumbnail>
</template>

<script>
    import AlbumThumbnail from './AlbumThumbnail.vue';

    export default {
        props: ['album'],

        computed: {
            avatar() {
                return this.album.avatar ? this.album.avatar.data : null;
            },

            username() {
                return this.album.user ? this.album.user.data.name : null;
            },

            count() {
                return this.album.publicPhotos ? this.album.publicPhotos.data.length : null;
            }
        },

        methods: {
            /**
             * Show gallery modal.
             * 
             * @param  {object} photo
             * @return {void}
             */
            showModal(photo) {
                eventDispatcher.$emit('show-modal', photo, this.album.id);
            },
        },

        components: { AlbumThumbnail }
    }
</script>