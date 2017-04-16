/**
 * Pagination window (based on the laravel UrlWindow class)
 */

class PageViewport {
    constructor(paginator) {
        // The pagination data
        this.paginator = paginator;
    }
 
     /**
     * Create a new Page viewport instance.
     *
     * @param  {Object} paginator
     * @param  {Number}  onEachSide
     * @return {Object}
     */
    static make(paginator, onEachSide = 3) {
        return new PageViewport(paginator).getSlider(onEachSide);
    }
  
    /**
     * Get the viewport of pages to be shown.
     *
     * @param  {Number}  onEachSide
     * @return {Object}
     */
    getSlider(onEachSide = 3) {
        if (this.lastPage() < (onEachSide * 2) + 6) {
            return this.getSmallSlider();
        }

        return this.getPageSlider(onEachSide);
    }
  
    /**
     * Get the slider of pages there are not enough pages to slide.
     *
     * @return {Object}
     */
    getSmallSlider() {
        return {
            first  : this.getPageRange(1, this.lastPage()),
            slider : null,
            last   : null,
        };
    }
  
    /**
     * Create a page slider.
     *
     * @param  {Number}  onEachSide
     * @return {Object}
     */
    getPageSlider(onEachSide) {
        var viewport = onEachSide * 2;

        if (! this.hasPages()) {
            return {
                first  : null,
                slider : null,
                last   : null,
            };
        }

        // If the current page is very close to the beginning of the page range, we will
        // just render the beginning of the page range, followed by the last pages in this
        // list, since we will not have room to create a full slider.
        if (this.currentPage() <= viewport) {
            return this.getSliderTooCloseToBeginning(viewport);
        }

        // If the current page is close to the ending of the page range we will just get
        // this first pages, followed by a larger viewport of these ending pages
        // since we're too close to the end of the list to create a full on slider.            
        else if (this.currentPage() > (this.lastPage() - viewport)) {
            return this.getSliderTooCloseToEnding(viewport);
        }

        // If we have enough room on both sides of the current page to build a slider we
        // will surround it with both the beginning and ending caps, with this viewport
        // of pages in the middle providing a Google style sliding paginator setup.
        return this.getFullSlider(onEachSide);
    }
  

    /**
     * Get the slider of pages when too close to beginning of viewport.
     *
     * @param  {Number}  viewport
     * @return {Object}
     */
    getSliderTooCloseToBeginning(viewport) {
        return {
            first  : this.getPageRange(1, viewport + 2),
            slider : null,
            last   : this.getFinish(),
        };
    }

    /**
     * Get the slider of pages when too close to ending of viewport.
     *
     * @param  {Number}  viewport
     * @return {Object}
     */  
    getSliderTooCloseToEnding(viewport) {
        var last = this.getPageRange(
            this.lastPage() - (viewport + 2),
            this.lastPage()
        );
    
        return {
            first  : this.getStart(),
            slider : null,
            last   : last,
        };     
    }

    /**
     * Get the slider of pages when a full slider can be made.
     *
     * @param  {Number}  onEachSide
     * @return {Object}
     */  
    getFullSlider(onEachSide) {
        return {
            first  : this.getStart(),
            slider : this.getAdjacentPageRange(onEachSide),
            last   : this.getFinish(),
        }; 
    }

    /**
     * Get the page range for the current page viewport.
     *
     * @param  {Number}  onEachSide
     * @return {Array}
     */  
    getAdjacentPageRange(onEachSide) {
        return this.getPageRange(
            this.currentPage() - onEachSide,
            this.currentPage() + onEachSide
        );
    }

    /**
     * Create a range of pagination pages.
     *
     * @param  {Number}  start
     * @param  {Number}  end
     * @return {Array}
     */  
    getPageRange(start, end) {
        var array = [];
    
        for(var i = start; i <= end; i++) {
            array.push(i);
        }

        return array;
    }

    /**
     * Get the starting pages of a pagination slider.
     *
     * @return {Array}
     */
    getStart() {
        return this.getPageRange(1, 1);  
    }

    /**
     * Get the ending pages of a pagination slider.
     *
     * @return {Array}
     */  
    getFinish() {
        return this.getPageRange(
            this.lastPage(),
            this.lastPage()
        );
    }

    /**
     * Determine if the underlying paginator being presented has pages to show.
     *
     * @return {Boolean}
     */  
    hasPages() {
        return this.lastPage() > 1;
    }

    /**
     * Get the current page from the paginator.
     *
     * @return {Number}
     */
    currentPage() {
        return this.paginator.currentPage;
    }

    /**
     * Get the last page from the paginator.
     *
     * @return {Number}
     */
    lastPage() {
        return this.paginator.lastPage;
    }
}

export default PageViewport;