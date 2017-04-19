<template>
    <div>
        <div class="columns is-multiline">
            <div class="column is-4 has-text-centered" v-for="album in albums">
                <album :album="album"></album>
                <modal :album="album" :photos="album.publicPhotos.data" 
                    :current-photo="album.avatar ? album.avatar.data : null">
                </modal>
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
             * @return void
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
             * @param  {object} data
             * @return {void}     
             */
            parseData(data) {
                this.paginator = FractalPaginator.make(data.meta.pagination);
                this.albums = data.data;
            },

            /**
             * Navigate through the pagination.
             * 
             * @param  number page
             * @return void
             */
            navigate(page) 
            {
                this.filters['page'] = page;
                this.fetchAlbums();
            },
        },

        components: { Album }
    }
</script>