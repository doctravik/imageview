<template>
    <div class="control" @click="activate">
        <span>
            <slot name="icon"></slot>
        </span>
    </div>
</template>

<script>
    export default {
        methods: {
            /**
             * Activate control.
             * 
             * @return {void}
             */
            activate() {
                this.$emit('activate');
            },
        }
    }
</script>

<style scoped>
    .control {
        padding: 10px 15px;
    }

    .control i {
        vertical-align: baseline;
    }

    .control:hover {
        cursor: pointer;
        background-color: whitesmoke;
    }
</style>