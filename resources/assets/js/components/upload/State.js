class State
{
    /** 
     * Create an instance of State.
     * 
     * @param  {Array}  states
     * @param  {string} current
     * @return {void}
     */
    constructor(states = [], current) {
      this.states = this.normalize(states);
      this.current = this.get(current);
        
      this.make();
    }
  
    /**
     * Normalize states.
     * 
     * @param  {string} states
     * @return {object}
     */
    normalize(states) {
      let normalizedStates = {};
      
      states.forEach((state, index) => {
        normalizedStates[state.toLowerCase()] = index;
      });
      
      return normalizedStates;
    }
  
    /**
     * Make state checkers dynamicly.
     * 
     * @return {this}
     */
    make() {
      for(let state in this.states) {
      
        this['is' + this.capitalize(state)] = function() {
            return this.current === this.states[state];
        }
      }
      
      return this;
    }
  
    /**
     * Set state as a current.
     * 
     * @param {void} state
     */
    set(state) {
        this.current = this.get(state.toLowerCase());
    }

    /**
     * Check if states has the given state key.
     * 
     * @param  {string}  state
     * @return {boolean}
     */
    has(state) {
        return this.states.hasOwnProperty(state);
    }

    /**
     * Get state by key.
     * 
     * @param  {string} state
     * @return {mixed}
     */
    get(state) {
        return  this.has(state) ? this.states[state] : null;
    }

    /**
     * Reset current state.
     * 
     * @return {void}
     */
    reset() {
        this.current = null;
    }

    /**
     * Capitalize first letter.
     * 
     * @param  {string} text
     * @return {string}
     */
    capitalize(text) {
        return text[0].toUpperCase() + text.slice(1);
    }
}

export default State;