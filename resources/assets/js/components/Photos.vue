<script>
    export default {
        props: ['album'],
        methods: {
            sendAlbum() {
                eventDispatcher.$emit('send-album', this.album);
            }
        }
    }
</script>