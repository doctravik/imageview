<template>
    <div>
        <div class="columns is-multiline">
            <div class="column is-4 has-text-centered" v-for="photo in photos">
                <thumbnail :photo="photo" :album-slug="album.slug" size="small" 
                    @activate-thumbnail="showModal"
                    @photo-was-deleted="deletePhoto"
                    @reset-avatars='resetAvatars'>
                </thumbnail>
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

            /**
             * Add new photo to photos.
             *  
             * @param {object} photo
             * @return void
             */
            addPhoto(photo) {
                this.photos.push(photo);                
            },

            /**
             * Remove photo from photos.
             * 
             * @param  {integer} photoId
             * @return {void}
             */
            deletePhoto(photoId) {
                this.photos = this.photos.filter(photo => photo.id != photoId);
            },

            /**
             * Reset avatars property of all photos.
             * 
             * @param  {integer} photoId
             * @return {void}
             */
            resetAvatars(photoId) {
                this.photos.forEach(photo => {
                    if(photo.id != photoId) {
                        photo.is_avatar = false;
                    }
                });
            },

            listenEvents() {
                eventDispatcher.$on('file-was-uploaded', photo => {
                    if(photo.album.data.id === this.album.id) {
                        this.addPhoto(photo);
                    }
                });
            }
        }
    }
</script>