/**
 * Get url for path.
 * 
 * @param  string path
 * @return string
 */
function url(path) {
    return Laravel.storage.replace(/\/+$/, '/') + path;
}

/**
* Capitalize first letter.
* 
* @param  {string} text
* @return {string}
*/
function capitalize(text) {
    return text[0].toUpperCase() + text.slice(1);
}

export { url, capitalize };