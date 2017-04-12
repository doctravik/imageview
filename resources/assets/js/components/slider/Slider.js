class Slider
{
    constructor(viewport, length, start = 0, active = null) {
        this.start = start;
        this.viewport = viewport;
        this.length = length;
        this.active = active;
    }

    /**
     * Number of rooms adjacent to active room.
     * 
     * @return {integer}
     */
    onEachSide() {
        return Math.floor((this.viewport - 1) / 2);
    }

    /**
     * Set start element.
     * 
     * @param  {integer} index
     * @return {void}
     */
    setStart(index) {
        this.start = index;
    }

    /**
     * Getter for end.
     * 
     * @return {integer}
     */
    end() {
        return this.start + this.step();
    }

    /**
     * Distance between the first and the last element of viewport.
     * 
     * @return {integer}
     */
    step() {
        return this.viewport - 1;
    }

    /**
     * Index of the last item of the slider.
     * 
     * @return {integer}
     */
    lastElement() {
        return this.length ? this.length - 1 : 0;
    }

    /**
     * Move slider to the left.
     * 
     * @return {void}
     */
    prev() {
        if(this.willBeOutOfLeftBorder()) {
            this.start = 0;
        } else {
            this.start -= this.step();
        }
    }
    
    /**
     * Check if the very left element will be out of slider.
     *  
     * @return {boolean}
     */
    willBeOutOfLeftBorder() {
        return (this.start - this.step()) < 0;
    }

    /**
     * Move slider to the right.
     * 
     * @return {void}
     */
    next() {
        if(this.willBeOutOfRightBorder()) {
            this.start += this.lastElement() - this.end();
        } else {
            this.start += this.step();
        }
    }

    /**
     * Check if the very right element will be out of slider.
     *  
     * @return {boolean}
     */
    willBeOutOfRightBorder() {
        return (this.end() + this.step()) > this.lastElement();
    }

    /**
     * Check if slider has left items to be showed.
     * 
     * @return {boolean}
     */
    hasPrev() {
        return this.start > 0;
    }

    /**
     * Check if slider has right items to be showed.
     * 
     * @return {boolean}
     */
    hasNext() {
        return this.end() < this.lastElement();
    }

    /**
     * Set active element.
     * 
     * @param {integer} index
     * @return this
     */
    setActive(index) {
        this.active = index;

        return this;
    }

    /**
     * Get updated slider consider active item in the viewport.
     * 
     * @return {void}
     */
    update() {
        this.getSlider();
    }

    /**
     * Get slider.
     * 
     * @return {array}
     */
    getSlider() {
        if (this.lastElement() < this.viewport) {
            return this.getSliderTooCloseToBeginning();
        }
        
        return this.getFullSlider();
    }

    /**
     * Get full slider.
     * 
     * @return {array}
     */
    getFullSlider() {
        if (this.active < this.onEachSide()) {
            return this.getSliderTooCloseToBeginning();
        }

        else if (this.active > (this.lastElement() - this.onEachSide())) {
            return this.getSliderTooCloseToEnding();
        }

        return this.getAdjacentSlider();
    }

    /**
     * Get the slider when too close to beginning of viewport.
     *
     * @return {array}
     */
    getSliderTooCloseToBeginning() {
        this.start = 0;
        
        return this.getRange();
    }

    /**
     * Get the slider when too close to ending of viewport.
     *
     * @return {array}
     */  
    getSliderTooCloseToEnding() {
        this.start = this.lastElement() - this.step();

        return this.getRange();
    }

    /**
     * Get the slider when a full slider can be made.
     *
     * @return {array}
     */  
    getAdjacentSlider() {
        this.start = this.active - this.onEachSide();
        
        return this.getRange();
    }

    /**
     * Create a range of slider.
     *
     * @return {array}
     */  
    getRange() {
        return [this.start, this.end()];
    }
}

export default Slider;