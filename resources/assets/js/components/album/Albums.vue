<template>
    <div>
        <div class="columns is-multiline">
            <div class="column is-4 has-text-centered" v-for="album in albums" v-if="isVisible(album)">
                <album :album="album"></album>
            </div>
        </div>
        <hr v-if="hasPaginator">
        <pagination :paginator="paginator" v-if="hasPaginator" @page-was-changed="navigate"></pagination>
    </div>
</template>

<script>
    import Album from './Album.vue';
    import FractalPaginator from './../pagination/ServerFractalPaginator';

    export default {
        mounted() {
            this.fetchAlbums();
        },

        computed: {
            hasPaginator() {
                return this.paginator && this.paginator.lastPage > 1;
            }
        },

        data() {
            return {
                albums: null,
                paginator: null,
                filters: {}
            }
        }, 

        methods: {
            /**
             * Get all of the albums from db.
             * 
             * @return {Void}
             */
            fetchAlbums() {
                axios.get('/webapi/albums', { params: this.filters })
                    .then(response => {
                        this.parseData(response.data)
                    });
            },

            /**
             * Parse response data.
             * 
             * @param  {Object} data
             * @return {Void}     
             */
            parseData(data) {
                this.paginator = FractalPaginator.make(data.meta.pagination);
                this.albums = data.data;
            },

            /**
             * Navigate through the pagination.
             * 
             * @param  {Number} page
             * @return {Void}
             */
            navigate(page) 
            {
                this.filters['page'] = page;
                this.fetchAlbums();
            },

            /**
             * Check if album is visible.
             * 
             * @param  {Object}  album
             * @return {Boolean}
             */
            isVisible(album) {
                return album.publicPhotos.data.length && album.public;
            }
        },

        components: { Album }
    }
</script>