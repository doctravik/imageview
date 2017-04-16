<template>
    <nav class="pagination is-centered" v-if="paginator.lastPage > 1">
        <a tabindex="0" class="pagination-previous" :disabled="!hasPrev()"
            @click.prevent="prev">Previous</a>

        <a tabindex="0" class="pagination-next" :disabled="!hasNext()"
            @click.prevent="next">Next page</a>

        <ul class="pagination-list">
            <template v-for="(item, key) in pages">
                <li v-if="isArray(item) && key != 'first'">
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>

                <li v-for="page in item" @click.prevent="navigate(page)" tabindex="0">
                    <a :class="[ 'pagination-link', { 'is-current': isCurrent(page) }]">{{ page }}</a>
                </li>
            </template>
        </ul>
    </nav>
</template>

<script>
    import PageViewport from './PageViewport.js';

    export default {
        props: ['paginator'],

        computed: {
            pages: function() {
                return PageViewport.make(this.paginator);
            }
        },

        methods: {
            /**
             * Notify parent that page has been changed
             * 
             * @param  {Number} page
             * @return {Void}
             */
            navigate(page) {
                this.$emit('page-was-changed', page);
            },

            /**
             * Move to the previous page.
             * 
             * @return {Void}
             */
            prev() {
                if(this.hasPrev()) {
                    this.navigate(this.paginator.currentPage - 1);    
                }
            },

            /**
             * Move to the next page.
             * 
             * @return {Void}
             */
            next() {
                if(this.hasNext()) {
                    this.navigate(this.paginator.currentPage + 1);    
                }
            },

            /**
             * Check if there is a previous element.
             * 
             * @return boolean
             */
            hasPrev() {
                return this.paginator.currentPage > 1;
            },

            /**
             * Check if there is a next element.
             * 
             * @return boolean
             */
            hasNext() {
                return this.paginator.currentPage < this.paginator.lastPage;
            },

            /**
             * Check if the page is the current page.
             * 
             * @param  {Integer}  page
             * @return {Boolean} 
             */
            isCurrent(page) {
                return this.paginator.currentPage == page;
            },

            isArray(array) {
                return array instanceof Array;
            }
        }
    }
</script>