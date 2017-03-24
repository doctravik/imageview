<template>
    <a @click.prevent="showModal">
        <img :src="thumbnail" alt="photo">
    </a>
</template>

<script>
    export default {
        props: ['photo', 'thumbnail'],

        methods: {
            showModal() {
                eventDispatcher.$emit('show-modal', this.photo);
            }
        }
    }
</script>