webpackJsonp([0],[
/* 0 */
/***/ (function(module, exports) {

// this module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  scopeId,
  cssModules
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  // inject cssModules
  if (cssModules) {
    var computed = Object.create(options.computed || null)
    Object.keys(cssModules).forEach(function (key) {
      var module = cssModules[key]
      computed[key] = function () { return module }
    })
    options.computed = computed
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 1 */,
/* 2 */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function() {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		var result = [];
		for(var i = 0; i < this.length; i++) {
			var item = this[i];
			if(item[2]) {
				result.push("@media " + item[2] + "{" + item[1] + "}");
			} else {
				result.push(item[1]);
			}
		}
		return result.join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};


/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(97)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction) {
  isProduction = _isProduction

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[data-vue-ssr-id~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),
/* 4 */,
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__utilities_Helpers_js__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utilities_path_Parser_js__ = __webpack_require__(59);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__utilities_path_NodePathParser_js__ = __webpack_require__(58);




/* harmony default export */ __webpack_exports__["a"] = ({
    props: ['photo', 'size'],

    computed: {
        /**
         * Get url to the thumbnail.
         * 
         * @return {string}
         */
        url: function url() {
            var path = this.path(this.size);

            if (!path) {
                return '';
            }

            return this.getUrl(path);
        }
    },

    data: function data() {
        return {
            parser: new __WEBPACK_IMPORTED_MODULE_1__utilities_path_Parser_js__["a" /* default */](new __WEBPACK_IMPORTED_MODULE_2__utilities_path_NodePathParser_js__["a" /* default */]())
        };
    },


    methods: {
        /**
         * Show full photo.
         * 
         * @return {void}
         */
        activate: function activate() {
            this.$emit('activate-thumbnail', this.photo);
        },


        /**
         * Get path to the thumbnail.
         * 
         * @param  {string} size
         * @return {string}
         */
        path: function path(size) {
            if (!this.photo) {
                return '';
            }

            var segments = this.parser.parse(this.photo.path);

            return segments.dirname + '/' + segments.filename + '_' + size + segments.extname;
        },


        /**
         * Get url for the given path consider storage
         *
         * @param {string} path
         */
        getUrl: __WEBPACK_IMPORTED_MODULE_0__utilities_Helpers_js__["a" /* url */]
    }
});

/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return url; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return capitalize; });
/**
 * Get url for path.
 * 
 * @param  string path
 * @return string
 */
function url(path) {
    return Laravel.storage + path;
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



/***/ }),
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var State = function () {
  /** 
   * Create an instance of State.
   * 
   * @param  {Array}  states
   * @param  {string} current
   * @return {void}
   */
  function State() {
    var states = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
    var current = arguments[1];

    _classCallCheck(this, State);

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


  _createClass(State, [{
    key: 'normalize',
    value: function normalize(states) {
      var normalizedStates = {};

      states.forEach(function (state, index) {
        normalizedStates[state.toLowerCase()] = index;
      });

      return normalizedStates;
    }

    /**
     * Make state checkers dynamicly.
     * 
     * @return {this}
     */

  }, {
    key: 'make',
    value: function make() {
      var _this = this;

      var _loop = function _loop(state) {

        _this['is' + _this.capitalize(state)] = function () {
          return this.current === this.states[state];
        };
      };

      for (var state in this.states) {
        _loop(state);
      }

      return this;
    }

    /**
     * Set state as a current.
     * 
     * @param {void} state
     */

  }, {
    key: 'set',
    value: function set(state) {
      this.current = this.get(state.toLowerCase());
    }

    /**
     * Check if states has the given state key.
     * 
     * @param  {string}  state
     * @return {boolean}
     */

  }, {
    key: 'has',
    value: function has(state) {
      return this.states.hasOwnProperty(state);
    }

    /**
     * Get state by key.
     * 
     * @param  {string} state
     * @return {mixed}
     */

  }, {
    key: 'get',
    value: function get(state) {
      return this.has(state) ? this.states[state] : null;
    }

    /**
     * Reset current state.
     * 
     * @return {void}
     */

  }, {
    key: 'reset',
    value: function reset() {
      this.current = null;
    }

    /**
     * Capitalize first letter.
     * 
     * @param  {string} text
     * @return {string}
     */

  }, {
    key: 'capitalize',
    value: function capitalize(text) {
      return text[0].toUpperCase() + text.slice(1);
    }
  }]);

  return State;
}();

/* harmony default export */ __webpack_exports__["a"] = (State);

/***/ }),
/* 17 */,
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(45),
  /* template */
  __webpack_require__(92),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/slider/Slider.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Slider.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-f2adfbd2", Component.options)
  } else {
    hotAPI.reload("data-v-f2adfbd2", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

__webpack_require__(52);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('albums', __webpack_require__(70));
Vue.component('modal', __webpack_require__(66));
Vue.component('thumbnail', __webpack_require__(74));
Vue.component('slider-thumbnail', __webpack_require__(73));
Vue.component('photos', __webpack_require__(67));
Vue.component('slider', __webpack_require__(18));
Vue.component('upload-form', __webpack_require__(78));
Vue.component('file', __webpack_require__(76));
Vue.component('statistics', __webpack_require__(77));
Vue.component('pagination', __webpack_require__(72));

var app = new Vue({
  el: '#app'
});

/***/ }),
/* 20 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__slider_Slider_vue__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__slider_Slider_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__slider_Slider_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utilities_Collection__ = __webpack_require__(56);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__utilities_Helpers__ = __webpack_require__(6);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['album', 'photos'],

    data: function data() {
        return {
            isActive: false,
            showSlider: true,
            gallery: new __WEBPACK_IMPORTED_MODULE_1__utilities_Collection__["a" /* default */](this.photos)
        };
    },
    mounted: function mounted() {
        this.listenEvents();
    },


    methods: {
        /**
         * Listen events.
         * 
         * @return {void}
         */
        listenEvents: function listenEvents() {
            var _this = this;

            eventDispatcher.$on('show-modal', function (photo, albumId) {
                if (albumId === _this.album.id) {
                    _this.setCurrentPhoto(photo);
                    _this.updateGallery();
                    _this.updateSlider();
                    _this.open();
                } else {
                    _this.close();
                }
            });
        },


        /**
         * Select photo.
         * 
         * @param {object} photo
         * @return {void}
         */
        setCurrentPhoto: function setCurrentPhoto(photo) {
            if (photo && photo.hasOwnProperty('id')) {
                this.setCurrentIndex(this.getIndexById(photo.id));
            }
        },


        /**
         * Set index as a current
         * 
         * @param {number} index
         * @return {void}
         */
        setCurrentIndex: function setCurrentIndex(index) {
            this.gallery.setCursor(index);
        },


        /**
         * Get index of photo in the gallery.
         * 
         * @param  {number} photoId
         * @return {number}
         */
        getIndexById: function getIndexById(photoId) {
            return this.photos.map(function (item) {
                return item.id;
            }).indexOf(photoId);
        },


        /**
         * Update slider.
         * 
         * @return {void}
         */
        updateSlider: function updateSlider() {
            eventDispatcher.$emit('update-slider', this.album.id, this.gallery.current);
        },


        /**
         * Update Gallery.
         * 
         * @return {void}
         */
        updateGallery: function updateGallery() {
            this.gallery.setItems(this.photos);
        },


        /**
         * Show modal.
         * 
         * @return {void}
         */
        open: function open() {
            this.isActive = true;
        },


        /**
         * Close modal.
         * 
         * @return {void}
         */
        close: function close() {
            this.isActive = false;
        },


        /**
         * Get prev photo of the gallery.
         * 
         * @return {void}
         */
        prev: function prev() {
            this.gallery.prev();
            this.updateSlider();
        },


        /**
         * Get next photo of the gallery.
         * 
         * @return {void}
         */
        next: function next() {
            this.gallery.next();
            this.updateSlider();
        },


        /**
         * Check if gallery has previous image.
         * 
         * @return {boolean}
         */
        hasPrev: function hasPrev() {
            return this.gallery.has(this.gallery.current - 1);
        },


        /**
         * Check if gallery has next image.
         * 
         * @return {boolean}
         */
        hasNext: function hasNext() {
            return this.gallery.has(this.gallery.current + 1);
        },


        /**
         * Get url of the current photo.
         *     
         * @return {string}
         */
        getCurrentUrl: function getCurrentUrl() {
            var photo = this.gallery.get(this.gallery.current);

            return photo ? __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_2__utilities_Helpers__["a" /* url */])(photo.path) : '';
        },


        /**
         * Toggle slider.
         * 
         * @return {void}
         */
        toggleSlider: function toggleSlider() {
            if (this.showSlider) {
                this.showSlider = false;
            } else {
                this.showSlider = true;
            }
        },


        /**
         * Get url of the given path.
         */
        url: __WEBPACK_IMPORTED_MODULE_2__utilities_Helpers__["a" /* url */]
    },

    сomponents: { Slider: __WEBPACK_IMPORTED_MODULE_0__slider_Slider_vue___default.a }
});

/***/ }),
/* 39 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuedraggable__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuedraggable___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vuedraggable__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['album'],

    data: function data() {
        return {
            photos: [],
            currentPhoto: null
        };
    },
    mounted: function mounted() {
        this.fetchPhotos();
        this.listenEvents();
    },


    methods: {
        /**
         * Sort photos by order.
         * 
         * @return {void}
         */
        sort: function sort() {
            this.photos.map(function (photo, index) {
                photo.sort_order = index + 1;
            });
            this.updateServerOrder();
        },


        /**
         * Update photos order in db.
         * 
         * @return {void}
         */
        updateServerOrder: function updateServerOrder() {
            axios.patch('/webapi/albums/' + this.album.slug + '/photos/sorting', {
                photos: this.photos
            });
        },


        /**
         * Show gallery modal.
         * 
         * @param  {object} photo
         * @return {void}
         */
        showModal: function showModal(photo) {
            this.currentPhoto = photo;
            eventDispatcher.$emit('show-modal', photo, this.album.id);
        },


        /**
         * Fetch photos from server.
         * 
         * @return {Promise}
         */
        fetchPhotos: function fetchPhotos() {
            var _this = this;

            axios.get('/webapi/albums/' + this.album.slug + '/photos').then(function (response) {
                _this.photos = response.data.data;
            });
        },


        /**
         * Add new photo to photos.
         *  
         * @param {object} photo
         * @return void
         */
        addPhoto: function addPhoto(photo) {
            this.photos.push(photo);
        },


        /**
         * Remove photo from photos.
         * 
         * @param  {integer} photoId
         * @return {void}
         */
        deletePhoto: function deletePhoto(photoId) {
            this.photos = this.photos.filter(function (photo) {
                return photo.id != photoId;
            });
        },


        /**
         * Reset avatars property of all photos.
         * 
         * @param  {integer} photoId
         * @return {void}
         */
        resetAvatars: function resetAvatars(photoId) {
            this.photos.forEach(function (photo) {
                if (photo.id != photoId) {
                    photo.is_avatar = false;
                }
            });
        },
        listenEvents: function listenEvents() {
            var _this2 = this;

            eventDispatcher.$on('file-was-uploaded', function (photo) {
                if (photo.album.data.id === _this2.album.id) {
                    _this2.addPhoto(photo);
                }
            });
        }
    },

    components: { Draggable: __WEBPACK_IMPORTED_MODULE_0_vuedraggable___default.a }
});

/***/ }),
/* 40 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__AlbumThumbnail_vue__ = __webpack_require__(69);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__AlbumThumbnail_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__AlbumThumbnail_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['album'],

    computed: {
        avatar: function avatar() {
            return this.album.avatar ? this.album.avatar.data : null;
        },
        username: function username() {
            return this.album.user ? this.album.user.data.name : null;
        },
        count: function count() {
            return this.album.publicPhotos ? this.album.publicPhotos.data.length : null;
        }
    },

    methods: {
        /**
         * Show gallery modal.
         * 
         * @param  {object} photo
         * @return {void}
         */
        showModal: function showModal(photo) {
            eventDispatcher.$emit('show-modal', photo, this.album.id);
        }
    },

    components: { AlbumThumbnail: __WEBPACK_IMPORTED_MODULE_0__AlbumThumbnail_vue___default.a }
});

/***/ }),
/* 41 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_pluralize__ = __webpack_require__(8);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_pluralize___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_pluralize__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__mixins_Thumbnail_js__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__utilities_Helpers_js__ = __webpack_require__(6);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['album', 'count', 'user'],

    data: function data() {
        return {
            showThumbnailMenu: false
        };
    },


    methods: {
        /**
         * Show full photo.
         * 
         * @return {void}
         */
        activate: function activate() {
            this.$emit('activate-thumbnail', this.photo);
        },


        pluralize: __WEBPACK_IMPORTED_MODULE_0_pluralize___default.a, capitalize: __WEBPACK_IMPORTED_MODULE_2__utilities_Helpers_js__["b" /* capitalize */]
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_1__mixins_Thumbnail_js__["a" /* default */]]
});

/***/ }),
/* 42 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Album_vue__ = __webpack_require__(68);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Album_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__Album_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__pagination_ServerFractalPaginator__ = __webpack_require__(54);
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
    mounted: function mounted() {
        this.fetchAlbums();
    },


    computed: {
        hasPaginator: function hasPaginator() {
            return this.paginator && this.paginator.lastPage > 1;
        }
    },

    data: function data() {
        return {
            albums: null,
            paginator: null,
            filters: {}
        };
    },


    methods: {
        /**
         * Get all of the albums from db.
         * 
         * @return void
         */
        fetchAlbums: function fetchAlbums() {
            var _this = this;

            axios.get('/webapi/albums', { params: this.filters }).then(function (response) {
                _this.parseData(response.data);
            });
        },


        /**
         * Parse response data.
         * 
         * @param  {object} data
         * @return {void}     
         */
        parseData: function parseData(data) {
            this.paginator = __WEBPACK_IMPORTED_MODULE_1__pagination_ServerFractalPaginator__["a" /* default */].make(data.meta.pagination);
            this.albums = data.data;
        },


        /**
         * Navigate through the pagination.
         * 
         * @param  number page
         * @return void
         */
        navigate: function navigate(page) {
            this.filters['page'] = page;
            this.fetchAlbums();
        }
    },

    components: { Album: __WEBPACK_IMPORTED_MODULE_0__Album_vue___default.a }
});

/***/ }),
/* 43 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    props: {
        title: { default: '' },
        classStyle: { default: '' }
    },

    methods: {
        /**
         * Activate control.
         * 
         * @return {void}
         */
        activate: function activate() {
            this.$emit('activate');
        }
    }
});

/***/ }),
/* 44 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__PageViewport_js__ = __webpack_require__(53);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['paginator'],

    computed: {
        pages: function pages() {
            return __WEBPACK_IMPORTED_MODULE_0__PageViewport_js__["a" /* default */].make(this.paginator);
        }
    },

    methods: {
        /**
         * Notify parent that page has been changed
         * 
         * @param  {Number} page
         * @return {Void}
         */
        navigate: function navigate(page) {
            this.$emit('page-was-changed', page);
        },


        /**
         * Move to the previous page.
         * 
         * @return {Void}
         */
        prev: function prev() {
            if (this.hasPrev()) {
                this.navigate(this.paginator.currentPage - 1);
            }
        },


        /**
         * Move to the next page.
         * 
         * @return {Void}
         */
        next: function next() {
            if (this.hasNext()) {
                this.navigate(this.paginator.currentPage + 1);
            }
        },


        /**
         * Check if there is a previous element.
         * 
         * @return boolean
         */
        hasPrev: function hasPrev() {
            return this.paginator.currentPage > 1;
        },


        /**
         * Check if there is a next element.
         * 
         * @return boolean
         */
        hasNext: function hasNext() {
            return this.paginator.currentPage < this.paginator.lastPage;
        },


        /**
         * Check if the page is the current page.
         * 
         * @param  {Integer}  page
         * @return {Boolean} 
         */
        isCurrent: function isCurrent(page) {
            return this.paginator.currentPage == page;
        },
        isArray: function isArray(array) {
            return array instanceof Array;
        }
    }
});

/***/ }),
/* 45 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Slider_js__ = __webpack_require__(55);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['album', 'photos', 'showSlider'],

    computed: {
        slider: function slider() {
            return new __WEBPACK_IMPORTED_MODULE_0__Slider_js__["a" /* default */](5, this.photos.length);
        }
    },

    data: function data() {
        return {
            sliderPhotos: [],
            currentPhotoIndex: null
        };
    },
    mounted: function mounted() {
        this.listenEvents();
    },


    methods: {
        /**
         * Move slider to the left.
         * 
         * @return {void}
         */
        prev: function prev() {
            if (this.slider.hasPrev()) {
                this.slider.prev();
                this.updateSliderPhotos();
            }
        },


        /**
         * Move slider to the right.
         * 
         * @return {void}
         */
        next: function next() {
            if (this.slider.hasNext()) {
                this.slider.next();
                this.updateSliderPhotos();
            }
        },


        /**
         * Update photos in the slider viewport.
         * 
         * @return {void}
         */
        updateSliderPhotos: function updateSliderPhotos() {
            this.sliderPhotos = this.photos.slice(this.slider.start, this.slider.end() + 1);
        },


        /**
         * Activate photo without change slider position
         * 
         * @param  {object} photo
         * @return {void}
         */
        selectPhoto: function selectPhoto(photo) {
            this.currentPhotoIndex = this.getIndexById(photo.id);

            this.$emit('change-photo', this.currentPhotoIndex);
        },


        /**
         * Get photo index by it's id.
         * 
         * @param  {integer} id
         * @return {integer}
         */
        getIndexById: function getIndexById(id) {
            return this.photos.map(function (photo) {
                return photo.id;
            }).indexOf(id);
        },


        /**
         * Update slider with active element.
         * 
         * @return {void}
         */
        updateSlider: function updateSlider() {
            this.slider.setActive(this.currentPhotoIndex).update();
        },


        /**
         * Check if photo is activated.
         *   
         * @param  {integer}  id
         * @return {boolean}
         */
        isActive: function isActive(id) {
            return this.currentPhotoIndex === this.getIndexById(id);
        },


        /**
         * Listen events.
         * 
         * @return {void}
         */
        listenEvents: function listenEvents() {
            var _this = this;

            eventDispatcher.$on('update-slider', function (id, index) {
                if (id === _this.album.id) {
                    _this.currentPhotoIndex = index;
                    _this.updateSlider();
                    _this.updateSliderPhotos();
                }
            });
        }
    }
});

/***/ }),
/* 46 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mixins_Thumbnail_js__ = __webpack_require__(5);
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_0__mixins_Thumbnail_js__["a" /* default */]]
});

/***/ }),
/* 47 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mixins_Thumbnail_js__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ThumbnailMenu_vue__ = __webpack_require__(75);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ThumbnailMenu_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__ThumbnailMenu_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            showThumbnailMenu: false
        };
    },


    methods: {
        deletePhoto: function deletePhoto(photoId) {
            this.$emit('delete-photo', photoId);
        },
        resetAvatars: function resetAvatars(photoId) {
            this.$emit('reset-avatars', photoId);
        }
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_0__mixins_Thumbnail_js__["a" /* default */]],

    components: { ThumbnailMenu: __WEBPACK_IMPORTED_MODULE_1__ThumbnailMenu_vue___default.a }
});

/***/ }),
/* 48 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__menu_Control_vue__ = __webpack_require__(71);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__menu_Control_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__menu_Control_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['isActive', 'photo'],

    computed: {
        isPublic: function isPublic() {
            return this.photo.is_public;
        },
        isAvatar: function isAvatar() {
            return this.photo.is_avatar;
        }
    },

    methods: {
        /**
         * Execute action.
         * 
         * @param  {string} action
         */
        execute: function execute(action) {
            if (typeof this[action] == 'function') {
                this[action]();
            }
        },


        /**
         * Delete photo from database.
         * 
         * @return {void}
         */
        delete: function _delete() {
            var _this = this;

            axios.delete('/webapi/photos/' + this.photo.slug).then(function (response) {
                _this.$emit('photo-was-deleted', _this.photo.id);
            });
        },


        /**
         * Update visibility.
         *
         * @return void
         */
        visibility: function visibility() {
            var data = {};

            if (this.isPublic) {
                data.is_public = false;
            } else {
                data.is_public = true;
            }

            this.updatePhoto(data);
        },


        /**
         * Update photo in database.
         *
         * @param {object}
         * @return {void}
         */
        updatePhoto: function updatePhoto(data) {
            var _this2 = this;

            axios.patch('/webapi/photos/' + this.photo.slug, data).then(function (response) {
                ;
                _this2.updateClientPhoto(response.data.data);
            });
        },


        /**
         * Toggle avatar property.
         * 
         * @return {void}
         */
        avatar: function avatar() {
            var _this3 = this;

            axios.patch('/webapi/photos/' + this.photo.slug + '/avatars').then(function (response) {
                ;
                _this3.$emit('reset-avatars', _this3.photo.id);
                _this3.updateClientPhoto(response.data.data);
            });
        },


        /**
         * Update photo on client side.
         * 
         * @param  {object} photo
         * @return {void}      
         */
        updateClientPhoto: function updateClientPhoto(photo) {
            for (var prop in photo) {
                this.photo[prop] = photo[prop];
            }
        }
    },

    components: { Control: __WEBPACK_IMPORTED_MODULE_0__menu_Control_vue___default.a }
});

/***/ }),
/* 49 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__State_js__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utilities_Errors_js__ = __webpack_require__(57);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



var CancelToken = axios.CancelToken;

/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['file', 'endpoint', 'inputName'],

    mounted: function mounted() {
        this.upload();
        this.listenEvents();
    },
    data: function data() {
        return {
            progress: 0,
            cancel: null,
            errors: new __WEBPACK_IMPORTED_MODULE_1__utilities_Errors_js__["a" /* default */](),
            state: new __WEBPACK_IMPORTED_MODULE_0__State_js__["a" /* default */](['initial', 'uploading', 'success', 'failed', 'cancelled'])
        };
    },


    methods: {
        /**
         * Upload file.
         *     
         * @return {void}
         */
        upload: function upload() {
            this.state.set('uploading');

            axios.post(this.endpoint, this.form(), this.config()).then(this.onSuccess).catch(this.onFail);
        },


        /**
         * Get form data.
         * 
         * @return {FormData}
         */
        form: function form() {
            var form = new FormData();

            form.append(this.inputName, this.file);

            return form;
        },


        /**
         * Get request config.
         * 
         * @return {object}
         */
        config: function config() {
            var _this = this;

            return {
                onUploadProgress: function onUploadProgress(progressEvent) {
                    _this.progress = Math.round(progressEvent.loaded * 100 / progressEvent.total);
                },
                cancelToken: new CancelToken(function executor(c) {
                    this.cancel = c;
                }.bind(this))
            };
        },


        /**
         * Handle a successful file uploading.
         * 
         * @param {object} response
         * @return {void}
         */
        onSuccess: function onSuccess(response) {
            eventDispatcher.$emit('file-was-uploaded', response.data.data);

            this.done('success');
        },


        /**
         * Handle a failed form submission.
         * 
         * @param {object} error
         * @return {void}
         */
        onFail: function onFail(error) {
            if (axios.isCancel(error)) {
                this.done('cancelled');
            } else {
                this.errors.record(error.response.data);
                this.done('failed');
            }
        },


        /**
         * Complete uploading.
         * 
         * @param  {string}   state
         * @return {void}
         */
        done: function done(state) {
            this.state.set(state);
            eventDispatcher.$emit('file-was-processed');
        },


        /**
         * Cancel upload.
         * 
         * @return {void}
         */
        cancelUpload: function cancelUpload() {
            this.cancel('Operation canceled by the user.');
        },


        /**
         * Listen events.
         * 
         * @return {void}
         */
        listenEvents: function listenEvents() {
            var _this2 = this;

            eventDispatcher.$on('cancel-uploading', function () {
                _this2.cancelUpload();
            });
        }
    }
});

/***/ }),
/* 50 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['total'],

    computed: {
        uploading: function uploading() {
            return this.total - this.uploaded;
        },
        isAllDone: function isAllDone() {
            return this.total === this.uploaded;
        }
    },

    mounted: function mounted() {
        var _this = this;

        eventDispatcher.$on('file-was-processed', function () {
            _this.update();
        });

        eventDispatcher.$on('reset', function () {
            _this.uploaded = 0;
        });
    },
    data: function data() {
        return {
            uploaded: 0
        };
    },


    methods: {
        update: function update() {
            this.uploaded++;

            if (this.isAllDone) {
                eventDispatcher.$emit('all-files-were-uploaded');
            }
        }
    }
});

/***/ }),
/* 51 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__State_js__ = __webpack_require__(16);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    props: ['album'],

    data: function data() {
        return {
            files: [],
            isDraggedOver: false,
            fileInputName: 'photo',
            state: new __WEBPACK_IMPORTED_MODULE_0__State_js__["a" /* default */](['initial', 'uploading', 'done']),
            endpoint: '/webapi/albums/' + this.album.slug + '/photos'
        };
    },
    mounted: function mounted() {
        this.state.set('initial');
        this.listenEvents();
    },


    methods: {
        /**
         * Cancel all uploads.
         * 
         * @return {void}
         */
        cancel: function cancel() {
            eventDispatcher.$emit('cancel-uploading');
        },


        /**
         * Reset upload form.
         * 
         * @return {void}
         */
        reset: function reset() {
            this.state.set('initial');
            this.files = [];
            this.$refs.form.reset();
            eventDispatcher.$emit('reset');
        },


        /**
         * Dragenter the form.
         * 
         * @return {void}
         */
        enter: function enter() {
            if (this.state.isInitial()) {
                this.isDraggedOver = true;
            }
        },


        /**
         * Dragleave the form.
         * 
         * @return {void}
         */
        leave: function leave() {
            if (this.state.isInitial()) {
                this.isDraggedOver = false;
            }
        },


        /**
         * Dragdrop the form.
         * 
         * @param  {Event} e
         * @return {void}
         */
        drop: function drop(e) {
            var files = e.dataTransfer.files;

            if (this.state.isInitial() && files.length) {
                this.addFiles(files);
            }

            this.leave();
        },


        /**
         * Select the photo.
         * 
         * @param  {Event} e
         * @return {void}
         */
        select: function select(e) {
            this.addFiles(this.$refs.input.files);
        },


        /**
         * Add files to uploading.
         * 
         * @param {FileList} files
         */
        addFiles: function addFiles(files) {
            this.state.set('uploading');

            for (var i = 0; i < files.length; i++) {
                this.files.push(files[i]);
            }
        },


        /**
         * Listen events.
         * 
         * @return {void}
         */
        listenEvents: function listenEvents() {
            var _this = this;

            eventDispatcher.$on('all-files-were-uploaded', function () {
                _this.state.set('done');
            });
        }
    }
});

/***/ }),
/* 52 */
/***/ (function(module, exports, __webpack_require__) {

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = __webpack_require__(9);

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = __webpack_require__(7);

window.axios.defaults.headers.common = {
  'X-CSRF-TOKEN': window.Laravel.csrfToken,
  'X-Requested-With': 'XMLHttpRequest'
};

window.eventDispatcher = new Vue();

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

/***/ }),
/* 53 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Pagination window (based on the laravel UrlWindow class)
 */

var PageViewport = function () {
    function PageViewport(paginator) {
        _classCallCheck(this, PageViewport);

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


    _createClass(PageViewport, [{
        key: "getSlider",


        /**
         * Get the viewport of pages to be shown.
         *
         * @param  {Number}  onEachSide
         * @return {Object}
         */
        value: function getSlider() {
            var onEachSide = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 3;

            if (this.lastPage() < onEachSide * 2 + 6) {
                return this.getSmallSlider();
            }

            return this.getPageSlider(onEachSide);
        }

        /**
         * Get the slider of pages there are not enough pages to slide.
         *
         * @return {Object}
         */

    }, {
        key: "getSmallSlider",
        value: function getSmallSlider() {
            return {
                first: this.getPageRange(1, this.lastPage()),
                slider: null,
                last: null
            };
        }

        /**
         * Create a page slider.
         *
         * @param  {Number}  onEachSide
         * @return {Object}
         */

    }, {
        key: "getPageSlider",
        value: function getPageSlider(onEachSide) {
            var viewport = onEachSide * 2;

            if (!this.hasPages()) {
                return {
                    first: null,
                    slider: null,
                    last: null
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
            else if (this.currentPage() > this.lastPage() - viewport) {
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

    }, {
        key: "getSliderTooCloseToBeginning",
        value: function getSliderTooCloseToBeginning(viewport) {
            return {
                first: this.getPageRange(1, viewport + 2),
                slider: null,
                last: this.getFinish()
            };
        }

        /**
         * Get the slider of pages when too close to ending of viewport.
         *
         * @param  {Number}  viewport
         * @return {Object}
         */

    }, {
        key: "getSliderTooCloseToEnding",
        value: function getSliderTooCloseToEnding(viewport) {
            var last = this.getPageRange(this.lastPage() - (viewport + 2), this.lastPage());

            return {
                first: this.getStart(),
                slider: null,
                last: last
            };
        }

        /**
         * Get the slider of pages when a full slider can be made.
         *
         * @param  {Number}  onEachSide
         * @return {Object}
         */

    }, {
        key: "getFullSlider",
        value: function getFullSlider(onEachSide) {
            return {
                first: this.getStart(),
                slider: this.getAdjacentPageRange(onEachSide),
                last: this.getFinish()
            };
        }

        /**
         * Get the page range for the current page viewport.
         *
         * @param  {Number}  onEachSide
         * @return {Array}
         */

    }, {
        key: "getAdjacentPageRange",
        value: function getAdjacentPageRange(onEachSide) {
            return this.getPageRange(this.currentPage() - onEachSide, this.currentPage() + onEachSide);
        }

        /**
         * Create a range of pagination pages.
         *
         * @param  {Number}  start
         * @param  {Number}  end
         * @return {Array}
         */

    }, {
        key: "getPageRange",
        value: function getPageRange(start, end) {
            var array = [];

            for (var i = start; i <= end; i++) {
                array.push(i);
            }

            return array;
        }

        /**
         * Get the starting pages of a pagination slider.
         *
         * @return {Array}
         */

    }, {
        key: "getStart",
        value: function getStart() {
            return this.getPageRange(1, 1);
        }

        /**
         * Get the ending pages of a pagination slider.
         *
         * @return {Array}
         */

    }, {
        key: "getFinish",
        value: function getFinish() {
            return this.getPageRange(this.lastPage(), this.lastPage());
        }

        /**
         * Determine if the underlying paginator being presented has pages to show.
         *
         * @return {Boolean}
         */

    }, {
        key: "hasPages",
        value: function hasPages() {
            return this.lastPage() > 1;
        }

        /**
         * Get the current page from the paginator.
         *
         * @return {Number}
         */

    }, {
        key: "currentPage",
        value: function currentPage() {
            return this.paginator.currentPage;
        }

        /**
         * Get the last page from the paginator.
         *
         * @return {Number}
         */

    }, {
        key: "lastPage",
        value: function lastPage() {
            return this.paginator.lastPage;
        }
    }], [{
        key: "make",
        value: function make(paginator) {
            var onEachSide = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 3;

            return new PageViewport(paginator).getSlider(onEachSide);
        }
    }]);

    return PageViewport;
}();

/* harmony default export */ __webpack_exports__["a"] = (PageViewport);

/***/ }),
/* 54 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ServerFractalPaginator = function () {
    /**
     * Create a new server Fractal paginator instance.
     * 
     * @param  object paginator
     * @return void
     */
    function ServerFractalPaginator(paginator) {
        _classCallCheck(this, ServerFractalPaginator);

        this.paginator = paginator;
    }

    _createClass(ServerFractalPaginator, [{
        key: 'adapt',
        value: function adapt() {
            // console.log(this.paginator.current_page);
            return {
                'items': [],
                'currentPage': this.paginator.current_page,
                'lastPage': this.paginator.total_pages,
                'perPage': this.paginator.per_page,
                'total': this.paginator.total
            };
        }
    }], [{
        key: 'make',
        value: function make(paginator) {
            return new ServerFractalPaginator(paginator).adapt();
        }
    }]);

    return ServerFractalPaginator;
}();

/* harmony default export */ __webpack_exports__["a"] = (ServerFractalPaginator);

/***/ }),
/* 55 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Slider = function () {
    function Slider(viewport, length) {
        var start = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
        var active = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;

        _classCallCheck(this, Slider);

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


    _createClass(Slider, [{
        key: "onEachSide",
        value: function onEachSide() {
            return Math.floor((this.viewport - 1) / 2);
        }

        /**
         * Set start element.
         * 
         * @param  {integer} index
         * @return {void}
         */

    }, {
        key: "setStart",
        value: function setStart(index) {
            this.start = index;
        }

        /**
         * Getter for end.
         * 
         * @return {integer}
         */

    }, {
        key: "end",
        value: function end() {
            return this.start + this.step();
        }

        /**
         * Distance between the first and the last element of viewport.
         * 
         * @return {integer}
         */

    }, {
        key: "step",
        value: function step() {
            return this.viewport - 1;
        }

        /**
         * Index of the last item of the slider.
         * 
         * @return {integer}
         */

    }, {
        key: "lastElement",
        value: function lastElement() {
            return this.length ? this.length - 1 : 0;
        }

        /**
         * Move slider to the left.
         * 
         * @return {void}
         */

    }, {
        key: "prev",
        value: function prev() {
            if (this.willBeOutOfLeftBorder()) {
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

    }, {
        key: "willBeOutOfLeftBorder",
        value: function willBeOutOfLeftBorder() {
            return this.start - this.step() < 0;
        }

        /**
         * Move slider to the right.
         * 
         * @return {void}
         */

    }, {
        key: "next",
        value: function next() {
            if (this.willBeOutOfRightBorder()) {
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

    }, {
        key: "willBeOutOfRightBorder",
        value: function willBeOutOfRightBorder() {
            return this.end() + this.step() > this.lastElement();
        }

        /**
         * Check if slider has left items to be showed.
         * 
         * @return {boolean}
         */

    }, {
        key: "hasPrev",
        value: function hasPrev() {
            return this.start > 0;
        }

        /**
         * Check if slider has right items to be showed.
         * 
         * @return {boolean}
         */

    }, {
        key: "hasNext",
        value: function hasNext() {
            return this.end() < this.lastElement();
        }

        /**
         * Set active element.
         * 
         * @param {integer} index
         * @return this
         */

    }, {
        key: "setActive",
        value: function setActive(index) {
            this.active = index;

            return this;
        }

        /**
         * Get updated slider consider active item in the viewport.
         * 
         * @return {void}
         */

    }, {
        key: "update",
        value: function update() {
            this.getSlider();
        }

        /**
         * Get slider.
         * 
         * @return {array}
         */

    }, {
        key: "getSlider",
        value: function getSlider() {
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

    }, {
        key: "getFullSlider",
        value: function getFullSlider() {
            if (this.active < this.onEachSide()) {
                return this.getSliderTooCloseToBeginning();
            } else if (this.active > this.lastElement() - this.onEachSide()) {
                return this.getSliderTooCloseToEnding();
            }

            return this.getAdjacentSlider();
        }

        /**
         * Get the slider when too close to beginning of viewport.
         *
         * @return {array}
         */

    }, {
        key: "getSliderTooCloseToBeginning",
        value: function getSliderTooCloseToBeginning() {
            this.start = 0;

            return this.getRange();
        }

        /**
         * Get the slider when too close to ending of viewport.
         *
         * @return {array}
         */

    }, {
        key: "getSliderTooCloseToEnding",
        value: function getSliderTooCloseToEnding() {
            this.start = this.lastElement() - this.step();

            return this.getRange();
        }

        /**
         * Get the slider when a full slider can be made.
         *
         * @return {array}
         */

    }, {
        key: "getAdjacentSlider",
        value: function getAdjacentSlider() {
            this.start = this.active - this.onEachSide();

            return this.getRange();
        }

        /**
         * Create a range of slider.
         *
         * @return {array}
         */

    }, {
        key: "getRange",
        value: function getRange() {
            return [this.start, this.end()];
        }
    }]);

    return Slider;
}();

/* harmony default export */ __webpack_exports__["a"] = (Slider);

/***/ }),
/* 56 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Collection = function () {
    function Collection() {
        var items = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];

        _classCallCheck(this, Collection);

        this.items = items;
        this.current = items.length ? 0 : null;
    }

    /**
     * Set cursor.
     * 
     * @param {integer} index
     * @return this
     */


    _createClass(Collection, [{
        key: "setCursor",
        value: function setCursor(index) {
            this.current = index;

            return this;
        }

        /**
         * Set items.
         * @param {array} items
         * @return this
         */

    }, {
        key: "setItems",
        value: function setItems(items) {
            this.items = items;

            return this;
        }

        /**
         * Move cursor back.
         * 
         * @return {void}
         */

    }, {
        key: "prev",
        value: function prev() {
            if (this.has(this.current - 1)) {
                this.current--;
            }
        }

        /**
         * Move cursor forward.
         * 
         * @return {void}
         */

    }, {
        key: "next",
        value: function next() {
            if (this.has(this.current + 1)) {
                this.current++;
            }
        }

        /**
         * Check if collection has key.
         * 
         * @param  {integer}  index
         * @return {boolean}
         */

    }, {
        key: "has",
        value: function has(index) {
            return this.items.hasOwnProperty(index);
        }

        /**
         * Get item by index.
         * @param  {integer} index
         * @return {mixed}
         */

    }, {
        key: "get",
        value: function get(index) {
            return this.has(index) ? this.items[index] : undefined;
        }
    }]);

    return Collection;
}();

/* harmony default export */ __webpack_exports__["a"] = (Collection);

/***/ }),
/* 57 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Errors = function () {
    /**
     * Create a new Errors instance.
     */
    function Errors() {
        _classCallCheck(this, Errors);

        this.errors = {};
    }

    /**
     * Determine if an errors exists for the given field.
     *
     * @param {string} field
     */


    _createClass(Errors, [{
        key: "has",
        value: function has(field) {
            return this.errors.hasOwnProperty(field);
        }

        /**
         * Determine if we have any errors.
         */

    }, {
        key: "any",
        value: function any() {
            return Object.keys(this.errors).length > 0;
        }

        /**
         * Retrieve the error message for a field.
         *
         * @param {string} field
         */

    }, {
        key: "get",
        value: function get(field) {
            if (this.errors[field]) {
                return this.errors[field][0];
            }
        }

        /**
         * Record the new errors.
         *
         * @param {object} errors
         */

    }, {
        key: "record",
        value: function record(errors) {
            this.errors = errors;
        }

        /**
         * Clear one or all error fields.
         *
         * @param {string|null} field
         */

    }, {
        key: "clear",
        value: function clear(field) {
            if (field) {
                delete this.errors[field];

                return;
            }

            this.errors = {};
        }
    }]);

    return Errors;
}();

/* harmony default export */ __webpack_exports__["a"] = (Errors);

/***/ }),
/* 58 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_path__ = __webpack_require__(64);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_path___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_path__);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }



var NodePathParser = function () {
    /**
     * Create instance of NodePathParser.
     * 
     * @return {void}
     */
    function NodePathParser() {
        _classCallCheck(this, NodePathParser);

        this.parser = __WEBPACK_IMPORTED_MODULE_0_path___default.a;
    }

    /**
     * Get basename.
     * 
     * @param  {string} path
     * @return {string}
     */


    _createClass(NodePathParser, [{
        key: 'basename',
        value: function basename(path) {
            return this.parser.basename(path);
        }

        /**
         * Get dirname.
         * 
         * @param  {string} path
         * @return {string}
         */

    }, {
        key: 'dirname',
        value: function dirname(path) {
            return this.parser.dirname(path);
        }

        /**
         * Get filename.
         * 
         * @param  {string} path
         * @return {string}
         */

    }, {
        key: 'filename',
        value: function filename(path) {
            return this.basename(path).substring(0, this.basename(path).lastIndexOf('.'));
        }

        /**
         * Get extension.
         * 
         * @param  {string} path
         * @return {string}
         */

    }, {
        key: 'extname',
        value: function extname(path) {
            return this.parser.extname(path);
        }
    }]);

    return NodePathParser;
}();

/* harmony default export */ __webpack_exports__["a"] = (NodePathParser);

/***/ }),
/* 59 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Parser = function () {
    /**
     * Create instance of Parser.
     * @param  {object} parser
     * @return {void}
     */
    function Parser(parser) {
        _classCallCheck(this, Parser);

        this.parser = parser;
    }

    /**
     * Parse path.
     * 
     * @param  {string} path
     * @return {object}
     */


    _createClass(Parser, [{
        key: "parse",
        value: function parse(path) {
            return {
                dirname: this.parser.dirname(path),
                basename: this.parser.basename(path),
                filename: this.parser.filename(path),
                extname: this.parser.extname(path)
            };
        }
    }]);

    return Parser;
}();

/* harmony default export */ __webpack_exports__["a"] = (Parser);

/***/ }),
/* 60 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)();
exports.push([module.i, "\n.control[data-v-1fc3bcd6] {\n    padding: 10px 15px;\n}\n.control i[data-v-1fc3bcd6] {\n    vertical-align: baseline;\n}\n.control[data-v-1fc3bcd6]:hover {\n    cursor: pointer;\n    background-color: whitesmoke;\n    color: #000;\n}\n.control.active[data-v-1fc3bcd6] {\n    color: #00d1b2;\n}\n.control.active[data-v-1fc3bcd6]:hover {\n    color: #00a78e;\n}\n.control.disabled[data-v-1fc3bcd6] {\n    color: #bbb;\n}\n.control.disabled[data-v-1fc3bcd6]:hover {\n    color: #888;\n}\n", ""]);

/***/ }),
/* 61 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)();
exports.push([module.i, "\n.album__menu[data-v-505efc05] {\n    background-color: transparent;\n}\n.album__count[data-v-505efc05] {\n    color: white;\n    font-size: 20px;\n    font-weight: bold;\n}\n.album__name[data-v-505efc05] {\n    font-weight: bold;\n    margin-top: 16px;\n}\n.album__user[data-v-505efc05] {\n    font-size: 14px;\n}\n", ""]);

/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)();
exports.push([module.i, "\n.upload-file[data-v-7d421792] {\n    margin: 20px;\n    margin-top: 0;\n}\n.upload-file__cancel[data-v-7d421792] {\n    margin-left: 20px;\n}\n.upload-file__error[data-v-7d421792] {\n    font-size: .9em;\n}\n.upload-progress[data-v-7d421792] {\n    -webkit-box-align: center;\n        -ms-flex-align: center;\n            align-items: center;\n    display: -webkit-box;\n    display: -ms-flexbox;\n    display: flex;\n    position: relative;\n}\n.progress__bar[data-v-7d421792] {\n    background-color: #f5f5f5;\n    border-radius: 3px;\n    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);\n    height: 35px;\n    margin: 0;\n    position: relative;\n    width: 100%;\n}\n.progress__label[data-v-7d421792],\n.progress__percentage[data-v-7d421792] {\n    color: #333;\n    margin-left: 10px;\n    position: absolute;\n    top: 50%;\n    -webkit-transform: translate(0, -50%);\n            transform: translate(0, -50%);\n}\n.progress__percentage[data-v-7d421792] {\n    margin-right: 10px;\n    right: 0;\n}\n.progress__fill[data-v-7d421792] {\n    background-color: #42b983;\n    border-radius: 3px;\n    box-shadow: inset 0 -1px rgba(0, 0, 0, .15);\n    box-sizing: border-box;\n    height: 100%;\n    opacity: .6;\n    padding: 10px;\n    transition: width 500ms ease;\n}\n.progress__fill--failed[data-v-7d421792] {\n    background-color: #f66;\n    transition: none;\n    width: 100%!important;\n}\n.progress__fill--success[data-v-7d421792] {\n    opacity: 1;\n}\n", ""]);

/***/ }),
/* 63 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)();
exports.push([module.i, "\n.statistics[data-v-b383dd8e] {\n    display: block;\n    font-weight: bold;\n    margin-bottom: 20px;\n}\n", ""]);

/***/ }),
/* 64 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

// resolves . and .. elements in a path array with directory names there
// must be no slashes, empty elements, or device names (c:\) in the array
// (so also no leading and trailing slashes - it does not distinguish
// relative and absolute paths)
function normalizeArray(parts, allowAboveRoot) {
  // if the path tries to go above the root, `up` ends up > 0
  var up = 0;
  for (var i = parts.length - 1; i >= 0; i--) {
    var last = parts[i];
    if (last === '.') {
      parts.splice(i, 1);
    } else if (last === '..') {
      parts.splice(i, 1);
      up++;
    } else if (up) {
      parts.splice(i, 1);
      up--;
    }
  }

  // if the path is allowed to go above the root, restore leading ..s
  if (allowAboveRoot) {
    for (; up--; up) {
      parts.unshift('..');
    }
  }

  return parts;
}

// Split a filename into [root, dir, basename, ext], unix version
// 'root' is just a slash, or nothing.
var splitPathRe =
    /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/;
var splitPath = function(filename) {
  return splitPathRe.exec(filename).slice(1);
};

// path.resolve([from ...], to)
// posix version
exports.resolve = function() {
  var resolvedPath = '',
      resolvedAbsolute = false;

  for (var i = arguments.length - 1; i >= -1 && !resolvedAbsolute; i--) {
    var path = (i >= 0) ? arguments[i] : process.cwd();

    // Skip empty and invalid entries
    if (typeof path !== 'string') {
      throw new TypeError('Arguments to path.resolve must be strings');
    } else if (!path) {
      continue;
    }

    resolvedPath = path + '/' + resolvedPath;
    resolvedAbsolute = path.charAt(0) === '/';
  }

  // At this point the path should be resolved to a full absolute path, but
  // handle relative paths to be safe (might happen when process.cwd() fails)

  // Normalize the path
  resolvedPath = normalizeArray(filter(resolvedPath.split('/'), function(p) {
    return !!p;
  }), !resolvedAbsolute).join('/');

  return ((resolvedAbsolute ? '/' : '') + resolvedPath) || '.';
};

// path.normalize(path)
// posix version
exports.normalize = function(path) {
  var isAbsolute = exports.isAbsolute(path),
      trailingSlash = substr(path, -1) === '/';

  // Normalize the path
  path = normalizeArray(filter(path.split('/'), function(p) {
    return !!p;
  }), !isAbsolute).join('/');

  if (!path && !isAbsolute) {
    path = '.';
  }
  if (path && trailingSlash) {
    path += '/';
  }

  return (isAbsolute ? '/' : '') + path;
};

// posix version
exports.isAbsolute = function(path) {
  return path.charAt(0) === '/';
};

// posix version
exports.join = function() {
  var paths = Array.prototype.slice.call(arguments, 0);
  return exports.normalize(filter(paths, function(p, index) {
    if (typeof p !== 'string') {
      throw new TypeError('Arguments to path.join must be strings');
    }
    return p;
  }).join('/'));
};


// path.relative(from, to)
// posix version
exports.relative = function(from, to) {
  from = exports.resolve(from).substr(1);
  to = exports.resolve(to).substr(1);

  function trim(arr) {
    var start = 0;
    for (; start < arr.length; start++) {
      if (arr[start] !== '') break;
    }

    var end = arr.length - 1;
    for (; end >= 0; end--) {
      if (arr[end] !== '') break;
    }

    if (start > end) return [];
    return arr.slice(start, end - start + 1);
  }

  var fromParts = trim(from.split('/'));
  var toParts = trim(to.split('/'));

  var length = Math.min(fromParts.length, toParts.length);
  var samePartsLength = length;
  for (var i = 0; i < length; i++) {
    if (fromParts[i] !== toParts[i]) {
      samePartsLength = i;
      break;
    }
  }

  var outputParts = [];
  for (var i = samePartsLength; i < fromParts.length; i++) {
    outputParts.push('..');
  }

  outputParts = outputParts.concat(toParts.slice(samePartsLength));

  return outputParts.join('/');
};

exports.sep = '/';
exports.delimiter = ':';

exports.dirname = function(path) {
  var result = splitPath(path),
      root = result[0],
      dir = result[1];

  if (!root && !dir) {
    // No dirname whatsoever
    return '.';
  }

  if (dir) {
    // It has a dirname, strip trailing slash
    dir = dir.substr(0, dir.length - 1);
  }

  return root + dir;
};


exports.basename = function(path, ext) {
  var f = splitPath(path)[2];
  // TODO: make this comparison case-insensitive on windows?
  if (ext && f.substr(-1 * ext.length) === ext) {
    f = f.substr(0, f.length - ext.length);
  }
  return f;
};


exports.extname = function(path) {
  return splitPath(path)[3];
};

function filter (xs, f) {
    if (xs.filter) return xs.filter(f);
    var res = [];
    for (var i = 0; i < xs.length; i++) {
        if (f(xs[i], i, xs)) res.push(xs[i]);
    }
    return res;
}

// String.prototype.substr - negative index don't work in IE8
var substr = 'ab'.substr(-1) === 'b'
    ? function (str, start, len) { return str.substr(start, len) }
    : function (str, start, len) {
        if (start < 0) start = str.length + start;
        return str.substr(start, len);
    }
;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(17)))

/***/ }),
/* 65 */,
/* 66 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(38),
  /* template */
  __webpack_require__(80),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/Modal.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Modal.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0f354096", Component.options)
  } else {
    hotAPI.reload("data-v-0f354096", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(39),
  /* template */
  __webpack_require__(83),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/Photos.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Photos.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4fd15fa9", Component.options)
  } else {
    hotAPI.reload("data-v-4fd15fa9", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 68 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(40),
  /* template */
  __webpack_require__(89),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/album/Album.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Album.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-aa192a52", Component.options)
  } else {
    hotAPI.reload("data-v-aa192a52", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 69 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(94)

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(41),
  /* template */
  __webpack_require__(84),
  /* scopeId */
  "data-v-505efc05",
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/album/AlbumThumbnail.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] AlbumThumbnail.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-505efc05", Component.options)
  } else {
    hotAPI.reload("data-v-505efc05", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 70 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(42),
  /* template */
  __webpack_require__(88),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/album/Albums.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Albums.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-91b4dda8", Component.options)
  } else {
    hotAPI.reload("data-v-91b4dda8", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 71 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(93)

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(43),
  /* template */
  __webpack_require__(81),
  /* scopeId */
  "data-v-1fc3bcd6",
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/menu/Control.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Control.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1fc3bcd6", Component.options)
  } else {
    hotAPI.reload("data-v-1fc3bcd6", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(44),
  /* template */
  __webpack_require__(85),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/pagination/Pagination.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Pagination.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-6e7a6512", Component.options)
  } else {
    hotAPI.reload("data-v-6e7a6512", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(46),
  /* template */
  __webpack_require__(82),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/slider/SliderThumbnail.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] SliderThumbnail.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-345f8cc5", Component.options)
  } else {
    hotAPI.reload("data-v-345f8cc5", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 74 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(47),
  /* template */
  __webpack_require__(91),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/thumbnail/Thumbnail.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Thumbnail.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-c4128ade", Component.options)
  } else {
    hotAPI.reload("data-v-c4128ade", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 75 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(48),
  /* template */
  __webpack_require__(79),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/thumbnail/ThumbnailMenu.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] ThumbnailMenu.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-03f46fe0", Component.options)
  } else {
    hotAPI.reload("data-v-03f46fe0", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 76 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(95)

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(49),
  /* template */
  __webpack_require__(87),
  /* scopeId */
  "data-v-7d421792",
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/upload/File.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] File.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7d421792", Component.options)
  } else {
    hotAPI.reload("data-v-7d421792", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 77 */
/***/ (function(module, exports, __webpack_require__) {


/* styles */
__webpack_require__(96)

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(50),
  /* template */
  __webpack_require__(90),
  /* scopeId */
  "data-v-b383dd8e",
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/upload/Statistics.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] Statistics.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-b383dd8e", Component.options)
  } else {
    hotAPI.reload("data-v-b383dd8e", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 78 */
/***/ (function(module, exports, __webpack_require__) {

var Component = __webpack_require__(0)(
  /* script */
  __webpack_require__(51),
  /* template */
  __webpack_require__(86),
  /* scopeId */
  null,
  /* cssModules */
  null
)
Component.options.__file = "/var/www/imageview/resources/assets/js/components/upload/UploadForm.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {return key !== "default" && key !== "__esModule"})) {console.error("named exports are not supported in *.vue files.")}
if (Component.options.functional) {console.error("[vue-loader] UploadForm.vue: functional components are not supported with templates, they should use render functions.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-75fd957b", Component.options)
  } else {
    hotAPI.reload("data-v-75fd957b", Component.options)
  }
})()}

module.exports = Component.exports


/***/ }),
/* 79 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "thumbnail-menu"
  }, [_c('control', {
    attrs: {
      "title": "Delete photo"
    },
    on: {
      "activate": function($event) {
        _vm.execute('delete')
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-trash-o",
    attrs: {
      "aria-hidden": "true"
    },
    slot: "icon"
  })]), _vm._v(" "), (!_vm.isPublic) ? _c('control', {
    attrs: {
      "class-style": "disabled",
      "title": "Make public"
    },
    on: {
      "activate": function($event) {
        _vm.execute('visibility')
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-eye-slash",
    attrs: {
      "aria-hidden": "true"
    },
    slot: "icon"
  })]) : _vm._e(), _vm._v(" "), (_vm.isPublic) ? _c('control', {
    attrs: {
      "class-style": "active",
      "title": "Make private"
    },
    on: {
      "activate": function($event) {
        _vm.execute('visibility')
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-eye",
    attrs: {
      "aria-hidden": "true"
    },
    slot: "icon"
  })]) : _vm._e(), _vm._v(" "), (!_vm.isAvatar) ? _c('control', {
    attrs: {
      "class-style": "disabled",
      "title": "Make avatar"
    },
    on: {
      "activate": function($event) {
        _vm.execute('avatar')
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-check",
    attrs: {
      "aria-hidden": "true"
    },
    slot: "icon"
  })]) : _vm._e(), _vm._v(" "), (_vm.isAvatar) ? _c('control', {
    attrs: {
      "class-style": "active",
      "title": "Disable avatar"
    },
    on: {
      "activate": function($event) {
        _vm.execute('avatar')
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-check",
    attrs: {
      "aria-hidden": "true"
    },
    slot: "icon"
  })]) : _vm._e()], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-03f46fe0", module.exports)
  }
}

/***/ }),
/* 80 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: (_vm.isActive),
      expression: "isActive"
    }],
    staticClass: "gallery"
  }, [_c('div', {
    staticClass: "gallery__background"
  }), _vm._v(" "), _c('div', {
    staticClass: "gallery__wrapper"
  }, [_c('div', {
    staticClass: "gallery__content"
  }, [_c('div', {
    staticClass: "gallery__arrow--column"
  }, [(_vm.hasPrev()) ? _c('a', {
    staticClass: "gallery__arrow has-text-centered",
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.prev()
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-caret-left",
    attrs: {
      "aria-hidden": "true"
    }
  })]) : _vm._e()]), _vm._v(" "), (_vm.isActive) ? _c('div', {
    staticClass: "gallery__viewport"
  }, [_c('img', {
    staticClass: "gallery__image",
    attrs: {
      "src": _vm.getCurrentUrl()
    }
  })]) : _vm._e(), _vm._v(" "), _c('div', {
    staticClass: "gallery__arrow--column"
  }, [(_vm.hasNext()) ? _c('a', {
    staticClass: "gallery__arrow has-text-centered",
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.next()
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-caret-right",
    attrs: {
      "aria-hidden": "true"
    }
  })]) : _vm._e()])])]), _vm._v(" "), _c('slider', {
    attrs: {
      "album": _vm.album,
      "photos": _vm.photos,
      "show-slider": _vm.showSlider
    },
    on: {
      "change-photo": _vm.setCurrentIndex
    }
  }), _vm._v(" "), _c('button', {
    staticClass: "modal-close",
    on: {
      "click": _vm.close
    }
  }), _vm._v(" "), _c('span', {
    staticClass: "gallery__close-slider",
    attrs: {
      "title": _vm.showSlider ? 'Hide slider' : 'Show slider'
    },
    on: {
      "click": _vm.toggleSlider
    }
  }, [_c('i', {
    staticClass: "fa",
    class: [_vm.showSlider ? 'fa-toggle-on' : 'fa-toggle-off'],
    attrs: {
      "aria-hidden": "true"
    }
  })])], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-0f354096", module.exports)
  }
}

/***/ }),
/* 81 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "control",
    class: _vm.classStyle,
    attrs: {
      "title": _vm.title
    },
    on: {
      "click": _vm.activate
    }
  }, [_c('span', [_vm._t("icon")], 2)])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-1fc3bcd6", module.exports)
  }
}

/***/ }),
/* 82 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "slider_thumbnail",
    style: ({
      'background-image': 'url(' + _vm.url + ')'
    }),
    on: {
      "click": _vm.activate
    }
  })
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-345f8cc5", module.exports)
  }
}

/***/ }),
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('draggable', {
    staticClass: "columns is-multiline",
    attrs: {
      "options": {}
    },
    on: {
      "change": _vm.sort,
      "start": function($event) {
        _vm.drag = true
      },
      "end": function($event) {
        _vm.drag = false
      }
    },
    model: {
      value: (_vm.photos),
      callback: function($$v) {
        _vm.photos = $$v
      },
      expression: "photos"
    }
  }, _vm._l((_vm.photos), function(photo) {
    return _c('div', {
      staticClass: "column is-4 has-text-centered"
    }, [_c('thumbnail', {
      attrs: {
        "photo": photo,
        "size": "small"
      },
      on: {
        "activate-thumbnail": _vm.showModal,
        "delete-photo": _vm.deletePhoto,
        "reset-avatars": _vm.resetAvatars
      }
    })], 1)
  })), _vm._v(" "), _c('modal', {
    attrs: {
      "album": _vm.album,
      "photos": _vm.photos,
      "current-photo": _vm.currentPhoto
    }
  })], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-4fd15fa9", module.exports)
  }
}

/***/ }),
/* 84 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "album"
  }, [_c('div', {
    staticClass: "thumbnail"
  }, [_c('div', {
    staticClass: "thumbnail__mask",
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.activate($event)
      }
    }
  }), _vm._v(" "), _c('div', [(_vm.url) ? _c('img', {
    staticClass: "thumbnail__image",
    attrs: {
      "src": _vm.url,
      "alt": "thumbnail"
    }
  }) : _vm._e(), _vm._v(" "), (!_vm.url) ? _c('div', {
    staticClass: "thumbnail__default-image--small"
  }, [_c('span', [_vm._v("No avatar")])]) : _vm._e()]), _vm._v(" "), _c('div', {
    staticClass: "thumbnail-menu album__menu",
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.activate($event)
      }
    }
  }, [_c('div', {
    staticClass: "album__count"
  }, [_vm._v(_vm._s(_vm.pluralize('photo', _vm.count, true)))])])]), _vm._v(" "), _c('div', {
    staticClass: "album__name"
  }, [_vm._v(_vm._s(_vm.capitalize(_vm.album.name)))]), _vm._v(" "), _c('div', {
    staticClass: "album__user"
  }, [_vm._v(_vm._s(_vm.user))])])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-505efc05", module.exports)
  }
}

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return (_vm.paginator.lastPage > 1) ? _c('nav', {
    staticClass: "pagination is-centered"
  }, [_c('a', {
    staticClass: "pagination-previous",
    attrs: {
      "tabindex": "0",
      "disabled": !_vm.hasPrev()
    },
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.prev($event)
      }
    }
  }, [_vm._v("Previous")]), _vm._v(" "), _c('a', {
    staticClass: "pagination-next",
    attrs: {
      "tabindex": "0",
      "disabled": !_vm.hasNext()
    },
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.next($event)
      }
    }
  }, [_vm._v("Next page")]), _vm._v(" "), _c('ul', {
    staticClass: "pagination-list"
  }, [_vm._l((_vm.pages), function(item, key) {
    return [(_vm.isArray(item) && key != 'first') ? _c('li', [_c('span', {
      staticClass: "pagination-ellipsis"
    }, [_vm._v("…")])]) : _vm._e(), _vm._v(" "), _vm._l((item), function(page) {
      return _c('li', {
        attrs: {
          "tabindex": "0"
        },
        on: {
          "click": function($event) {
            $event.preventDefault();
            _vm.navigate(page)
          }
        }
      }, [_c('a', {
        class: ['pagination-link', {
          'is-current': _vm.isCurrent(page)
        }]
      }, [_vm._v(_vm._s(page))])])
    })]
  })], 2)]) : _vm._e()
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-6e7a6512", module.exports)
  }
}

/***/ }),
/* 86 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('div', {
    staticClass: "upload",
    class: {
      'upload--dragged': _vm.isDraggedOver
    },
    on: {
      "dragover": function($event) {
        $event.preventDefault();
        _vm.enter($event)
      },
      "dragenter": function($event) {
        $event.preventDefault();
        _vm.enter($event)
      },
      "dragleave": function($event) {
        $event.preventDefault();
        _vm.leave($event)
      },
      "dragend": function($event) {
        $event.preventDefault();
        _vm.leave($event)
      },
      "drop": function($event) {
        $event.preventDefault();
        _vm.drop($event)
      }
    }
  }, [(_vm.state.isInitial()) ? _c('label', {
    staticClass: "upload__area",
    attrs: {
      "for": "file"
    }
  }) : _vm._e(), _vm._v(" "), (_vm.state.isInitial()) ? _c('span', {
    staticClass: "upload__header"
  }, [_c('b', [_vm._v("Drag files here or click to select files")])]) : _vm._e(), _vm._v(" "), _c('form', {
    ref: "form"
  }, [_c('input', {
    ref: "input",
    staticClass: "upload__input",
    attrs: {
      "type": "file",
      "multiple": "",
      "id": "file"
    },
    on: {
      "change": _vm.select
    }
  })]), _vm._v(" "), (!_vm.state.isInitial()) ? _c('statistics', {
    attrs: {
      "total": _vm.files.length
    }
  }) : _vm._e(), _vm._v(" "), _vm._l((_vm.files), function(file) {
    return _c('file', {
      key: file.name,
      attrs: {
        "file": file,
        "input-name": _vm.fileInputName,
        "endpoint": _vm.endpoint
      }
    })
  }), _vm._v(" "), (_vm.state.isDone()) ? _c('div', [_c('button', {
    staticClass: "button",
    on: {
      "click": _vm.reset
    }
  }, [_vm._v("Reset")])]) : _vm._e(), _vm._v(" "), (_vm.state.isUploading()) ? _c('div', [_c('button', {
    staticClass: "button",
    on: {
      "click": _vm.cancel
    }
  }, [_vm._v("Cancel")])]) : _vm._e()], 2)])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-75fd957b", module.exports)
  }
}

/***/ }),
/* 87 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "upload-file"
  }, [_c('div', {
    staticClass: "upload-progress"
  }, [_c('div', {
    staticClass: "progress__bar"
  }, [_c('div', {
    staticClass: "progress__label"
  }, [_vm._v(_vm._s(_vm.file.name))]), _vm._v(" "), _c('div', {
    class: {
      'progress__fill': true,
      'progress__fill--success': _vm.state.isSuccess(),
        'progress__fill--failed': _vm.state.isFailed() || _vm.state.isCancelled()
    },
    style: ({
      'width': _vm.progress + '%'
    })
  }), _vm._v(" "), _c('div', {
    staticClass: "progress__percentage"
  }, [(_vm.state.isFailed()) ? _c('span', [_vm._v("Failed")]) : _vm._e(), _vm._v(" "), (_vm.state.isSuccess()) ? _c('span', [_vm._v("Complete")]) : _vm._e(), _vm._v(" "), (_vm.state.isCancelled()) ? _c('span', [_vm._v("Cancelled")]) : _vm._e(), _vm._v(" "), (_vm.state.isUploading()) ? _c('span', [_vm._v(_vm._s(_vm.progress) + "%")]) : _vm._e()])]), _vm._v(" "), (_vm.state.isUploading()) ? _c('button', {
    staticClass: "delete upload-file__cancel",
    on: {
      "click": _vm.cancelUpload
    }
  }) : _vm._e()]), _vm._v(" "), (_vm.errors.has(_vm.inputName)) ? _c('div', {
    staticClass: "upload-file__error",
    domProps: {
      "textContent": _vm._s(_vm.errors.get(_vm.inputName))
    }
  }) : _vm._e()])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-7d421792", module.exports)
  }
}

/***/ }),
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('div', {
    staticClass: "columns is-multiline"
  }, _vm._l((_vm.albums), function(album) {
    return _c('div', {
      staticClass: "column is-4 has-text-centered"
    }, [(album.publicPhotos.data.length) ? _c('album', {
      attrs: {
        "album": album
      }
    }) : _vm._e()], 1)
  })), _vm._v(" "), (_vm.hasPaginator) ? _c('hr') : _vm._e(), _vm._v(" "), (_vm.hasPaginator) ? _c('pagination', {
    attrs: {
      "paginator": _vm.paginator
    },
    on: {
      "page-was-changed": _vm.navigate
    }
  }) : _vm._e()], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-91b4dda8", module.exports)
  }
}

/***/ }),
/* 89 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('album-thumbnail', {
    attrs: {
      "size": "small",
      "album": _vm.album,
      "photo": _vm.avatar,
      "user": _vm.username,
      "count": _vm.count
    },
    on: {
      "activate-thumbnail": _vm.showModal
    }
  }), _vm._v(" "), _c('modal', {
    attrs: {
      "album": _vm.album,
      "photos": _vm.album.publicPhotos.data,
      "current-photo": _vm.album.avatar ? _vm.album.avatar.data : null
    }
  })], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-aa192a52", module.exports)
  }
}

/***/ }),
/* 90 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('span', {
    staticClass: "statistics"
  }, [_vm._v("Proccessed: " + _vm._s(_vm.uploaded) + " from " + _vm._s(_vm.total) + " files")])])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-b383dd8e", module.exports)
  }
}

/***/ }),
/* 91 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "thumbnail"
  }, [_c('div', {
    staticClass: "thumbnail__mask",
    on: {
      "click": function($event) {
        $event.preventDefault();
        _vm.activate($event)
      }
    }
  }), _vm._v(" "), _c('div', [_c('img', {
    staticClass: "thumbnail__image",
    attrs: {
      "src": _vm.url,
      "alt": "thumbnail"
    }
  })]), _vm._v(" "), _c('thumbnail-menu', {
    attrs: {
      "photo": _vm.photo,
      "is-active": _vm.showThumbnailMenu
    },
    on: {
      "photo-was-deleted": _vm.deletePhoto,
      "reset-avatars": _vm.resetAvatars
    }
  })], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-c4128ade", module.exports)
  }
}

/***/ }),
/* 92 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return (_vm.showSlider) ? _c('div', {
    staticClass: "slider"
  }, [_c('div', {
    staticClass: "slider__viewport"
  }, [(_vm.slider.hasPrev()) ? _c('div', {
    staticClass: "slider__arrow slider__arrow--left",
    on: {
      "click": function($event) {
        _vm.prev()
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-caret-left",
    attrs: {
      "aria-hidden": "true"
    }
  })]) : _vm._e(), _vm._v(" "), _vm._l((_vm.sliderPhotos), function(photo) {
    return _c('div', [_c('div', {
      class: ['slider__item', {
        'slider__item--active': _vm.isActive(photo.id)
      }]
    }, [_c('slider-thumbnail', {
      attrs: {
        "photo": photo,
        "size": "small"
      },
      on: {
        "activate-thumbnail": _vm.selectPhoto
      }
    })], 1)])
  }), _vm._v(" "), (_vm.slider.hasNext()) ? _c('div', {
    staticClass: "slider__arrow slider__arrow--right",
    on: {
      "click": function($event) {
        _vm.next()
      }
    }
  }, [_c('i', {
    staticClass: "fa fa-caret-right",
    attrs: {
      "aria-hidden": "true"
    }
  })]) : _vm._e()], 2)]) : _vm._e()
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-f2adfbd2", module.exports)
  }
}

/***/ }),
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(60);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("f06af0a2", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-1fc3bcd6\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Control.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-1fc3bcd6\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Control.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 94 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(61);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("487cf356", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-505efc05\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AlbumThumbnail.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-505efc05\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AlbumThumbnail.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 95 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(62);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("3a9eb2ba", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-7d421792\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./File.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-7d421792\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./File.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 96 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(63);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("1c5f8138", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-b383dd8e\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Statistics.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"id\":\"data-v-b383dd8e\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Statistics.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 97 */
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),
/* 98 */,
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(19);
module.exports = __webpack_require__(20);


/***/ })
],[99]);