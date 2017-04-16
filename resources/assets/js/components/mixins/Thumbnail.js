import { url as getUrl} from './../../utilities/Helpers.js';
import Parser from './../../utilities/path/Parser.js';
import NodePathParser from './../../utilities/path/NodePathParser.js';

export default {
    props: ['photo', 'size'],

    computed: {
        /**
         * Get url to the thumbnail.
         * 
         * @return {string}
         */
        url() {
            let path = this.path(this.size);

            if(!path) {
                return '';
            }

            return this.getUrl(path);
        }
    },

    data() {
        return {
            parser: new Parser(new NodePathParser())
        }
    },

    methods: {
        /**
         * Show full photo.
         * 
         * @return {void}
         */
        activate() {
            this.$emit('activate-thumbnail', this.photo);
        },

        /**
         * Get path to the thumbnail.
         * 
         * @param  {string} size
         * @return {string}
         */
        path(size) {
            if(!this.photo) {
                return '';
            }

            let segments = this.parser.parse(this.photo.path);

            return segments.dirname + '/' + segments.filename + '_' + size + segments.extname;
        },

        /**
         * Get url for the given path consider storage
         *
         * @param {string} path
         */
        getUrl
    }
}