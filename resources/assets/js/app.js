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

Vue.component('modal', require('./components/Modal.vue'));
Vue.component('photo', require('./components/Photo.vue'));
Vue.component('photos', require('./components/Photos.vue'));
Vue.component('slider', require('./components/Slider.vue'));
Vue.component('upload-form', require('./components/upload/UploadForm.vue'));
Vue.component('file', require('./components/upload/File.vue'));
Vue.component('statistics', require('./components/upload/Statistics.vue'));

const app = new Vue({
    el: '#app',
});
