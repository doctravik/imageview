/**
 * Get url for path.
 * 
 * @param  string path
 * @return string
 */
function url(path) {
    return Laravel.storage + path;
}

export { url };