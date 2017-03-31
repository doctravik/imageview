<template>
    <div class="slider">
        <div class="slider__arrow" @click="prev()" v-if="slider.hasPrev()">
            <i class="fa fa-caret-left" aria-hidden="true"></i>
        </div>
        <div v-for="photo in sliderPhotos">
            <div :class="['slider__item', {'slider__item--active' : isActive(photo.id)}]" 
                :style="{ 'background-image': 'url('+ getUrl(photo.path, 'small') +')' }"
                @click="selectPhoto(photo.id)">
            </div>
        </div>
        <div class="slider__arrow" @click="next()" v-if="slider.hasNext()">
            <i class="fa fa-caret-right" aria-hidden="true"></i>
        </div>
    </div>
</template>

<script>
    import { url } from './../utilities/Helpers';
    import Path from './../utilities/Path';
    import Slider from './Slider.js';
    import NodePath from 'path';

    export default {
        props: ['album'],
        
        computed: {
            photos() {
                return this.album.photos;
            },
        },

        data() {
            return {
                sliderPhotos: [],
                currentPhotoIndex: 0,
                path: new Path(NodePath),
                slider: new Slider(5, this.album.photos.length)
            }
        },

        mounted() {
            this.listenEvents();
        },

        methods: {
            /**
             * Get url for thumbnail consider it's size.
             * 
             * @param  {string} path
             * @param  {string} size
             * @return {string}
             */
            getUrl(path, size) {
                let filepath = this.path.generate(path, size);

                return this.url(filepath);
            },

            /**
             * Move slider to the left.
             * 
             * @return {void}
             */
            prev() {
                if(this.slider.hasPrev()) {
                    this.slider.prev();
                    this.updateSliderPhotos();
                }
            },

            /**
             * Move slider to the right.
             * 
             * @return {void}
             */
            next() {
                if(this.slider.hasNext()) {
                    this.slider.next();
                    this.updateSliderPhotos();
                }
            },

            /**
             * Update photos in the slider viewport.
             * 
             * @return {void}
             */
            updateSliderPhotos() {
                this.sliderPhotos = this.photos.slice(
                    this.slider.start, 
                    this.slider.end() + 1
                );
            },

            /**
             * Activate photo.
             * 
             * @param  {integer} id
             * @return {void}
             */
            selectPhoto(id) {
                this.currentPhotoIndex = this.getIndexById(id);

                this.$emit('change-photo', this.currentPhotoIndex);
            },

            /**
             * Get photo index by it's id.
             * 
             * @param  {integer} id
             * @return {integer}
             */
            getIndexById(id) {
                return this.photos.map(photo => photo.id).indexOf(id);
            },

            /**
             * Update slider with active element.
             * 
             * @return {void}
             */
            updateSlider() {
                this.slider.setActive(this.currentPhotoIndex).update();
            },

            /**
             * Check if photo is activated.
             *   
             * @param  {integer}  id
             * @return {boolean}
             */
            isActive(id) {
                return this.currentPhotoIndex === this.getIndexById(id);
            },

            /**
             * Listen events.
             * 
             * @return {void}
             */
            listenEvents() {
                eventDispatcher.$on('update-slider', (id, index) => {
                    if(id === this.album.id) {
                        this.currentPhotoIndex = index;
                        this.updateSlider();
                        this.updateSliderPhotos();
                    }
                });
            },

            /**
             * Get url for the given path consider storage
             *
             * @param {string} path
             */
            url
        }
    }
</script>