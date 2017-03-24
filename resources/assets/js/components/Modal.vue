<template>
    <div class="modal is-active gallery" v-show="isActive">
        <div class="modal-background"></div>
        <div class="modal-content columns is-gapless is-marginless is-mobile gallery__content">
            <div class="column">
                <a class="photo-view__control has-text-centered" v-if="hasPrev()" @click.prevent="prev()">
                    <i class="fa fa-caret-left" aria-hidden="true"></i>
                </a> 
            </div>

            <div class="column is-10 has-text-centered">
                <img :src="getCurrentUrl()" class="gallery__image">
            </div>

            <div class="column has-text-centered">                
                <a class="photo-view__control has-text-centered" v-if="hasNext()" @click.prevent="next()">
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <button class="modal-close" @click="close"></button>

        <slider @change-photo="setCurrentPhoto"
            :album="album">
        </slider>
    </div>
</template>

<script>
    import Slider from './Slider.vue';
    import Gallery from './../utilities/Collection';
    import { url } from './../utilities/Helpers';

    export default {
        props: ['album'],
        
        computed: {
            photos() {
                return this.album.photos;
            }
        },
        
        data() {
            return {
                isActive: false,
                gallery: new Gallery(this.album.photos)
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
                    if(photo.album_id === this.album.id) {
                        let index = this.photos.map(item => item.id).indexOf(photo.id);
                        this.setCurrentPhoto(index);
                        this.updateSlider();
                        this.open();           
                    } else {
                        this.close();
                    }
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
             * Move to prev photo.
             * 
             * @return {void}
             */
            prev() {
                this.gallery.prev();
                this.updateSlider();
            },

            /**
             * Move to next photo.
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
             * Set current photo.number
             * 
             * @param {number} index
             * @return {void}
             */
            setCurrentPhoto(index) {
                this.gallery.setCursor(index);
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
             * Update slider.
             * 
             * @return {void}
             */
            updateSlider() {
                eventDispatcher.$emit('update-slider', this.album.id, this.gallery.current);
            },

            /**
             * Get url of the given path.
             */
            url
        },

        сomponents: { Slider }
    }
</script>
