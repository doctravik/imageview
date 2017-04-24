<template>
    <div class="album">
        <div class="thumbnail">
            <div class="thumbnail__mask" @click.prevent="activate"></div>

            <div>
                <img :src="url" alt="thumbnail" class="thumbnail__image" v-if="url">
                <div class="thumbnail__default-image--small" v-if="!url"><span>No avatar</span></div>
            </div>

            <div class="thumbnail-menu album__menu" @click.prevent="activate">
                <div class="album__count">{{ pluralize('photo', count, true) }}</div>
            </div>
        </div>
        <div class="album__name">{{ capitalize(album.name) }}</div>
        <div class="album__user">{{ user }}</div>
    </div>
</template>

<script>
    import pluralize from 'pluralize';
    import Thumbnail from './../mixins/Thumbnail.js';
    import { capitalize } from './../../utilities/Helpers.js';

    export default {
        props: ['album', 'count', 'user'],

        data() {
            return {
                showThumbnailMenu: false
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

            pluralize, capitalize
        },

        mixins: [Thumbnail],
    }
</script>

<style scoped>
    .album__menu {
        background-color: transparent;
    }

    .album__count {
        color: white;
        font-size: 20px;
        font-weight: bold;
    }

    .album__name {
        font-weight: bold;
        margin-top: 16px;
    }

    .album__user {
        font-size: 14px;
    }
</style>