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
             * Delete photo from database.
             * 
             * @return {void}
             */
            delete() {
                axios.delete('/webapi/photos/' + this.photo.slug)
                    .then(response => {
                        this.$emit('photo-was-deleted', this.photo.id);
                    });
            },

            /**
             * Update visibility.
             *
             * @return void
             */
            visibility() {
                let data = {};

                if(this.isPublic) {
                    data.is_public = false;
                } else {
                    data.is_public = true;
                }

                this.updatePhoto(data);
            },

            /**
             * Update photo in database.
             *
             * @param {object}
             * @return {void}
             */
            updatePhoto(data) {
                axios.patch('/webapi/photos/' + this.photo.slug, data)
                    .then(response => {;
                        this.updateClientPhoto(response.data.data);
                    });
            },

            /**
             * Toggle avatar property.
             * 
             * @return {void}
             */
            avatar() {
                axios.patch('/webapi/photos/' + this.photo.slug + '/avatars')
                    .then(response => {;
                        this.$emit('reset-avatars', this.photo.id);
                        this.updateClientPhoto(response.data.data);
                    });
            },

            /**
             * Update photo on client side.
             * 
             * @param  {object} photo
             * @return {void}      
             */
            updateClientPhoto(photo) {
                for(let prop in photo) {
                    this.photo[prop] = photo[prop];
                }
            }
        },

        components: { Control }
    }
</script>