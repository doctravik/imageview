import Parser from 'path';

class NodePathParser
{
    /**
     * Create instance of NodePathParser.
     * 
     * @return {void}
     */
    constructor() {
        this.parser = Parser;
    }

    /**
     * Get basename.
     * 
     * @param  {string} path
     * @return {string}
     */
    basename(path) {
        return this.parser.basename(path);
    }

    /**
     * Get dirname.
     * 
     * @param  {string} path
     * @return {string}
     */
    dirname(path) {
        return this.parser.dirname(path);
    }

    /**
     * Get filename.
     * 
     * @param  {string} path
     * @return {string}
     */
    filename(path) {
        return this.basename(path).substring(0, this.basename(path).lastIndexOf('.'));
    }

    /**
     * Get extension.
     * 
     * @param  {string} path
     * @return {string}
     */
    extname(path) {
        return this.parser.extname(path);
    }
}

export default NodePathParser;