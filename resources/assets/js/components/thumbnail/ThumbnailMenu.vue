<template>
    <div class="thumbnail-menu">
        <control @activate="execute('delete')">
            <i slot="icon" class="fa fa-trash-o" aria-hidden="true"></i>
        </control>
    </div>
</template>

<script>
    import Control from './../menu/Control.vue';

    export default {
        props: ['isActive'],

        methods: {
            /**
             * Execute action.
             * 
             * @param  {string} action
             * @return {void}
             */
            execute(action) {
                if (typeof this[action] == 'function') {
                    this[action]();
                }
            },

            /**
             * Notify parent about deleting photo.
             * 
             * @return {void}
             */
            delete() {
                this.$emit('delete-photo')
            },
        },

        components: { Control }
    }
</script>