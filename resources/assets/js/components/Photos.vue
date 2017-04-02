<template>
    <div>
        <div class="columns is-multiline">
            <div class="column is-4" v-for="photo in photos">
                <thumbnail :photo="photo" size="small" @activate-thumbnail="showModal"></thumbnail>
            </div>
        </div>
        <modal :album="album"></modal>
    </div>
</template>

<script>
    export default {
        props: ['album', 'photos'],

        methods: {
            showModal(photo) {
                eventDispatcher.$emit('show-modal', photo);
            },
        }
    }
</script>