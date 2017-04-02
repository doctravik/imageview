class Parser
{
    /**
     * Create instance of Parser.
     * @param  {object} parser
     * @return {void}
     */
    constructor(parser) {
        this.parser = parser;
    }

    /**
     * Parse path.
     * 
     * @param  {string} path
     * @return {object}
     */
    parse(path) {
        return {
            dirname: this.parser.dirname(path),
            basename: this.parser.basename(path),
            filename: this.parser.filename(path),
            extname: this.parser.extname(path)
        };
    }
}

export default Parser;