<template>
    <div>
        <span class="statistics">Proccessed: {{ uploaded }} from {{ total }} files</span>
    </div>
</template>

<script>
    export default {
        props: ['total'],
        
        computed: {
            uploading() {
                return this.total - this.uploaded;
            },

            isAllDone() {
                return this.total === this.uploaded;
            }
        },

        mounted() {
            eventDispatcher.$on('file-was-uploaded', () => {
                this.update();
            });

            eventDispatcher.$on('reset', () => {
                this.uploaded = 0;
            });
        },

        data() {
            return {
                uploaded: 0,
            }
        },

        methods: {
            update() {
                this.uploaded++;
                
                if(this.isAllDone) {
                    eventDispatcher.$emit('all-files-were-uploaded');
                }
            }
        }
    }
</script>

<style scoped>
    .statistics {
        display: block;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>