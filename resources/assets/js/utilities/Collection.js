class Collection
{
    constructor(items = []) {
        this.items = items;
        this.current = items.length ? 0 : null;
    }

    /**
     * Set cursor.
     * 
     * @param {integer} index
     * @return this
     */
    setCursor(index) {
        this.current = index;

        return this;
    }

    /**
     * Set items.
     * @param {array} items
     * @return this
     */
    setItems(items) {
        this.items = items;

        return this;
    }

    /**
     * Move cursor back.
     * 
     * @return {void}
     */
    prev() {
        if(this.has(this.current - 1)) {
            this.current--;
        }
    }

    /**
     * Move cursor forward.
     * 
     * @return {void}
     */
    next() {
        if(this.has(this.current + 1)) {
            this.current++;
        }
    }

    /**
     * Check if collection has key.
     * @param  {integer}  index
     * @return {boolean}
     */
    has(index) {
        return this.items.hasOwnProperty(index);
    }

    /**
     * Get item by index.
     * @param  {integer} index
     * @return {mixed}
     */
    get(index) {
        return this.has(index) ? this.items[index] : undefined;
    }
}

export default Collection;