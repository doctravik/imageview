<template>
    <div class="gallery" v-show="isActive">
        <div class="gallery__background"></div>
        <div class="gallery__wrapper">
            <div class="gallery__content">
                <div class="gallery__arrow--column">
                    <a class="gallery__arrow has-text-centered" v-if="hasPrev()" @click.prevent="prev()">
                        <i class="fa fa-caret-left" aria-hidden="true"></i>
                    </a> 
                </div>

                <div class="gallery__viewport">
                    <img :src="getCurrentUrl()" class="gallery__image">
                </div>

                <div class="gallery__arrow--column">
                    <a class="gallery__arrow has-text-centered" v-if="hasNext()" @click.prevent="next()">
                        <i class="fa fa-caret-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <slider @change-photo="setCurrentIndex"
            :album="album" :photos="photos" :show-slider="showSlider">
        </slider>

        <button class="modal-close" @click="close"></button>

        <span class="gallery__close-slider" :title="showSlider ? 'Hide slider' : 'Show slider'" @click="toggleSlider">
            <i class="fa" :class="[showSlider ? 'fa-toggle-on' : 'fa-toggle-off']" aria-hidden="true"></i>
        </span>
    </div>
</template>

<script>
    import Slider from './Slider.vue';
    import Gallery from './../utilities/Collection';
    import { url } from './../utilities/Helpers';

    export default {
        props: ['album', 'photos'],

        data() {
            return {
                isActive: false,
                showSlider: true,
                gallery: new Gallery(this.photos)
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
                eventDispatcher.$on('show-modal', (photo, albumId) => {
                    if(albumId === this.album.id) {
                        this.setCurrentPhoto(photo);
                        this.updateGallery();
                        this.updateSlider();
                        this.open();
                    } else {
                        this.close();
                    }
                });
            },


            /**
             * Select photo.
             * 
             * @param {object} photo
             * @return {void}
             */
            setCurrentPhoto(photo) {
                if(photo && photo.hasOwnProperty('id')) {
                    this.setCurrentIndex(this.getIndexById(photo.id));
                }
            },

            /**
             * Set index as a current
             * 
             * @param {number} index
             * @return {void}
             */
            setCurrentIndex(index) {
                this.gallery.setCursor(index);
            },

            /**
             * Get index of photo in the gallery.
             * 
             * @param  {number} photoId
             * @return {number}
             */
            getIndexById(photoId) {
                return this.photos.map(item => item.id).indexOf(photoId);
            },

            /**
             * Update slider.
             * 
             * @return {void}
             */
            updateSlider() {
                eventDispatcher.$emit('update-slider', this.album.id, this.gallery.current);
            },

            /**
             * Update Gallery.
             * 
             * @return {void}
             */
            updateGallery() {
                this.gallery.setItems(this.photos);
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
             * Get prev photo of the gallery.
             * 
             * @return {void}
             */
            prev() {
                this.gallery.prev();
                this.updateSlider();
            },

            /**
             * Get next photo of the gallery.
             * 
             * @return {void}
             */
            next() {
                this.gallery.next();
                this.updateSlider();
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
             * Get url of the current photo.
             *     
             * @return {string}
             */
            getCurrentUrl() {
                let photo = this.gallery.get(this.gallery.current);

                return photo ? url(photo.path) : '';
            },

            /**
             * Toggle slider.
             * 
             * @return {void}
             */
            toggleSlider() {
                if(this.showSlider) {
                    this.showSlider = false;
                } else {
                    this.showSlider = true;
                }
            },

            /**
             * Get url of the given path.
             */
            url
        },

        сomponents: { Slider }
    }
</script>
