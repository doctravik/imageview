<template>
    <div class="thumbnail-menu">
        <control @activate="execute('delete')" title="Delete photo">
            <i slot="icon" class="fa fa-trash-o" aria-hidden="true"></i>
        </control>
        <control @activate="execute('visibility')" class-style="disabled" title="Make public" v-if="!isPublic">
            <i slot="icon" class="fa fa-eye-slash" aria-hidden="true"></i>
        </control>
        <control @activate="execute('visibility')" class-style="active" title="Make private" v-if="isPublic">
            <i slot="icon" class="fa fa-eye" aria-hidden="true"></i>
        </control>
        <control @activate="execute('avatar')" class-style="disabled" title="Make avatar" v-if="!isAvatar">
            <i slot="icon" class="fa fa-check" aria-hidden="true"></i>
        </control>
        <control @activate="execute('avatar')" class-style="active" title="Disable avatar" v-if="isAvatar">
            <i slot="icon" class="fa fa-check" aria-hidden="true"></i>
        </control>
    </div>
</template>

<script>
    import Control from './../menu/Control.vue';

    export default {
        props: ['isActive', 'photo'],

        computed: {
            isPublic() {
                return this.photo.is_public;
            },

            isAvatar() {
                return this.photo.is_avatar;
            }
        },

        methods: {
            /**
             * Execute action.
             * 
             * @param  {string} action
             */
            execute(action) {
                if (typeof this[action] == 'function') {
                    this[action]();
                }
            },

            /**
             * Notify parent about deleting photo.
             */
            delete() {
                this.$emit('delete-photo')
            },

            /**
             * Notify parent about toggle visibility.
             */
            visibility() {
                if(this.isPublic) {
                    this.$emit('toggle-visibility', {'is_public': false});
                } else {
                    this.$emit('toggle-visibility', {'is_public': true});
                }
            },

            /**
             * Notify parent about toggle avatar.
             */
            avatar() {
                this.$emit('toggle-avatar');
            }
        },

        components: { Control }
    }
</script>