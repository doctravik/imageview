<template>
    <div>
        <div class="columns is-multiline">
            <div class="column is-4" v-for="photo in photos">
                <thumbnail :photo="photo" size="small" @activate-thumbnail="showModal"></thumbnail>
            </div>
        </div>
        <modal :album="album" :photos="photos" :current-photo="currentPhoto"></modal>
    </div>
</template>

<script>
    export default {
        props: ['album'],

        data() {
            return {
                photos: [],
                currentPhoto: null,
            }
        },

        mounted() {
            this.fetchPhotos();
            this.listenEvents();
        },

        methods: {
            /**
             * Show gallery modal.
             * 
             * @param  {object} photo
             * @return {void}
             */
            showModal(photo) {
                this.currentPhoto = photo;
                eventDispatcher.$emit('show-modal', photo, this.album.id);
            },

            /**
             * Fetch photos from server.
             * 
             * @return {Promise}
             */
            fetchPhotos() {
                axios.get('/webapi/albums/' + this.album.slug + '/photos')
                    .then(response => {
                        this.photos = response.data.data;
                    });
            },

            listenEvents() {
                eventDispatcher.$on('file-was-uploaded', photo => {
                    if(photo.album.data.id === this.album.id) {
                        this.photos.push(photo);
                    }
                });
            }
        }
    }
</script>