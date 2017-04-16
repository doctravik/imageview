import chunk from 'lodash/chunk';

class Paginator
{
    /**
     * Create a new paginator instance.
     *
     * @param  array  items
     * @param  number  perPage
     * @param  number  currentPage
     * @return void
     */
    constructor(items, currentPage = 1, perPage = 2)
    {
        /**
        * All of the items before slicing.
        *
        * @var array
        */  
        this.items = items;

        /**
         * The number of items to be shown per page.
         * 
         * @var number
         */
        this.perPage = perPage;
        
        /**
         * The total number of items before slicing.
         *
         * @var number
         */
        this.total = items.length

        /**
         * The last available page.
         * 
         * @var number
         */
        this.lastPage = Math.ceil(this.total / perPage);

        /**
         * Chunked array of the all items to given perPage
         * 
         * @var array
         */
        this.items = this.chunkItems();

        // Set current page and items for current page
        this.setCurrentPage(currentPage);
    }

    /**
     * Create a new Paginator instance.
     * 
     * @param  array items
     * @param  number perPage
     * @param  number currentPage
     * @return void
     */
    static make(items, currentPage, perPage) {
        return (new Paginator(items, currentPage, perPage));
    }

    /**
     * Chunk items per page.
     *
     * @return array
     */
    chunkItems()
    {
        return chunk(this.items, this.perPage);
    }

    /**
     * Set current page and items for current page
     *
     * @param  number currentPage
     * @return void
     */
    setCurrentPage(currentPage = 1) 
    {
        // console.log(currentPage);
        /**
         * The current page being "viewed".
         */
        this.currentPage = this.normalizePageNumber(currentPage);

        /**
         * Items for current page.
         */
        this.data = this.setDataFor(this.currentPage);
    }

    /**
     * Set data for the current page
     *
     * @param  number currentPage
     * @return mixed
     */
    setDataFor(currentPage)
    {
        return this.items[currentPage - 1] ? this.items[currentPage - 1] : this.items[0];
    }

    /**
     * Normalize page number to the valid value
     * 
     * @param  number page
     * @return number page
     */
    normalizePageNumber(page) {
        if (page > this.lastPage) {
            return this.lastPage;
        }
        
        if (page < 1) {
            return 1;
        }

        return page;
    }

    /**
     * Check if there is a previous element.
     * 
     * @return boolean
     */
    hasPrev() {
        return this.currentPage > 1;
    }

    /**
     * Check if there is a next element.
     * 
     * @return boolean
     */
    hasNext() {
        return this.currentPage < this.lastPage;
    }
}

export default Paginator;