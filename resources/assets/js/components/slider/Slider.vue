<template>
    <div class="slider" v-if="showSlider">
        <div class="slider__viewport">
            <div class="slider__arrow slider__arrow--left" @click="prev()" v-if="slider.hasPrev()">
                <i class="fa fa-caret-left" aria-hidden="true"></i>
            </div>
            <div v-for="photo in sliderPhotos">
                <div :class="['slider__item', {'slider__item--active' : isActive(photo.id)}]">
                    <slider-thumbnail :photo="photo" size="small" @activate-thumbnail="selectPhoto"></slider-thumbnail>
                </div>
            </div>
            <div class="slider__arrow slider__arrow--right" @click="next()" v-if="slider.hasNext()">
                <i class="fa fa-caret-right" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</template>

<script>
    import Slider from './Slider.js';

    export default {
        props: ['album', 'photos', 'showSlider'],
        
        computed: {
            slider() {
                return  new Slider(5, this.photos.length);
            }
        },

        data() {
            return {
                sliderPhotos: [],
                currentPhotoIndex: null
            }
        },

        mounted() {
            this.listenEvents();
        },

        methods: {
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
             * Activate photo without change slider position
             * 
             * @param  {object} photo
             * @return {void}
             */
            selectPhoto(photo) {
                this.currentPhotoIndex = this.getIndexById(photo.id);

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
        }
    }
</script>