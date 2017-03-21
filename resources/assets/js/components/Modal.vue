<template>
    <div class="modal is-active" v-show="isActive">
        <div class="modal-background"></div>
        <div class="modal-content columns is-marginless gallery__content">
            <div class="column">
                <a class="photo-view__control has-text-centered" v-if="hasPrev()" @click.prevent="gallery.prev()">
                    <i class="fa fa-caret-left" aria-hidden="true"></i>
                </a> 
            </div>

            <div class="column is-10 has-text-centered">
                <img :src="getCurrentUrl()" class="gallery__image">
            </div>

            <div class="column has-text-centered">                
                <a class="photo-view__control has-text-centered" v-if="hasNext()" @click.prevent="gallery.next()">
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <button class="modal-close" @click="close"></button>
    </div>
</template>

<script>
    import Gallery from './../utilities/Collection';
    import { url } from './../utilities/Helpers';

    export default {
        data() {
            return {
                photo: '',
                isActive: false,
                gallery: new Gallery()
            }
        },

        mounted() {
            this.listenEvents();
        },

        methods: {
            /**
             * Listen events.
             * 
             * @return {void}
             */
            listenEvents() {
                eventDispatcher.$on('show-modal', (photo) => {
                    this.photo = photo;
                    this.open();
                });

                eventDispatcher.$on('send-album', (album) => {
                    let index = album.map(photo => photo.path).indexOf(this.photo);

                    this.gallery.setItems(album).setCursor(index);
                });
            },

            /**
             * Show modal.
             * 
             * @return {void}
             */
            open() {
                this.isActive = true;
            },

            /**
             * Close modal.
             * 
             * @return {void}
             */
            close() {
                this.isActive = false;
            },

            /**
             * Check if gallery has previous image.
             * 
             * @return {boolean}
             */
            hasPrev() {
                return this.gallery.has(this.gallery.current - 1)
            },

            /**
             * Check if gallery has next image.
             * 
             * @return {boolean}
             */
            hasNext() {
                return this.gallery.has(this.gallery.current + 1)
            },

            /**
             * Get url of the current image.
             *     
             * @return {string}
             */
            getCurrentUrl() {
                let photo = this.gallery.get(this.gallery.current);

                return photo ? url(photo.path) : '';
            },

            /**
             * Get url of the given path.
             */
            url
        }
    }
</script>
