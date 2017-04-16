<template>
    <div class="control" :class="classStyle" :title="title" @click="activate">
        <span>
            <slot name="icon"></slot>
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            title: { default: ''},
            classStyle: { default: ''}
        },

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
        color: #000;
    }

    .control.active {
        color: #00d1b2;
    }

    .control.active:hover {
        color: #00a78e;
    }

    .control.disabled {
        color: #bbb;
    }

    .control.disabled:hover {
        color: #888;
    }
</style>