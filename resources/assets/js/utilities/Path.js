class Path
{
    /**
     * Create an instance of Path.
     * 
     * @param  {mixed} driver
     * @return {void} 
     */
    constructor(driver) {
        this.driver = driver;
    }

    /**
     * Parse path.
     * 
     * @param  {string} path
     * @return {object}
     */
    parse(path) {
        let basename = this.driver.basename(path);

        return {
            dir: this.driver.dirname(path),
            basename: basename,
            filename: basename.substring(0, basename.lastIndexOf('.')),
            extension: this.driver.extname(path)
        };
    }

    /**
     * Generate path for different size.
     * 
     * @param  {string} path
     * @param  {string} size
     * @return {string}
     */
    generate(path, size) {
        let segments = this.parse(path);

        return segments.dir + '/' + segments.filename + '_' + size + segments.extension;
    }
}

export default Path;