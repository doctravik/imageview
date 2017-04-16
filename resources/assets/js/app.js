/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('albums', require('./components/album/Albums.vue'));
Vue.component('modal', require('./components/Modal.vue'));
Vue.component('thumbnail', require('./components/thumbnail/Thumbnail.vue'));
Vue.component('slider-thumbnail', require('./components/slider/SliderThumbnail.vue'));
Vue.component('photos', require('./components/Photos.vue'));
Vue.component('slider', require('./components/slider/Slider.vue'));
Vue.component('upload-form', require('./components/upload/UploadForm.vue'));
Vue.component('file', require('./components/upload/File.vue'));
Vue.component('statistics', require('./components/upload/Statistics.vue'));
Vue.component('pagination', require('./components/pagination/Pagination.vue'));

const app = new Vue({
    el: '#app',
});
