<template>
    <a @click.prevent="showImage">
        <img :src="thumbnail" alt="photo">
    </a>
</template>

<script>
    export default {
        props: ['photo', 'thumbnail'],

        methods: {
            showImage() {
                eventDispatcher.$emit('show-modal', this.photo);
                this.$emit('show-modal');
            }
        }
    }
</script>