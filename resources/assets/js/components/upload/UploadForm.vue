<template>
    <div>
        <div class="upload"
            @dragover.prevent="enter"
            @dragenter.prevent="enter"
            @dragleave.prevent="leave"
            @dragend.prevent="leave"
            @drop.prevent="drop"
            :class="{ 'upload--dragged': isDraggedOver }">

            <label for="file" class="upload__area" v-if="state.isInitial()"></label>

            <span class="upload__header" v-if="state.isInitial()"><b>Drag files here or click to select files</b></span>

            <form ref="form">
                <input class="upload__input" type="file" multiple  
                    @change="select" ref="input" id="file">
            </form>

            <statistics :total="files.length" v-if="!state.isInitial()"></statistics>

            <file v-for="file in files" :key="file.name"
                :file="file"
                :input-name="fileInputName"
                :endpoint="endpoint">    
            </file>

            <div v-if="state.isDone()"><button class="button" @click="reset">Reset</button></div>
            <div v-if="state.isUploading()"><button class="button" @click="cancel">Cancel</button></div>
        </div>
    </div>
</template>

<script>
    import State from './State.js';

    export default {
        props: ['album'],

        data() {
            return {
                files: [],
                isDraggedOver: false,
                fileInputName: 'photo',
                state: new State(['initial', 'uploading', 'done']),
                endpoint: '/webapi/albums/' + this.album.slug + '/photos'
            }
        },
        
        mounted() {
            this.state.set('initial');
            this.listenEvents();
        },

        methods: {
            /**
             * Cancel all uploads.
             * 
             * @return {void}
             */
            cancel() {
                eventDispatcher.$emit('cancel-uploading');
            },

            /**
             * Reset upload form.
             * 
             * @return {void}
             */
            reset() {
                this.state.set('initial');
                this.files = [];
                this.$refs.form.reset();
                eventDispatcher.$emit('reset');
            },

            /**
             * Dragenter the form.
             * 
             * @return {void}
             */
            enter() {
                if(this.state.isInitial()) {
                    this.isDraggedOver = true;
                }
            },

            /**
             * Dragleave the form.
             * 
             * @return {void}
             */
            leave() {
                if(this.state.isInitial()) {
                    this.isDraggedOver = false;
                }
            },

            /**
             * Dragdrop the form.
             * 
             * @param  {Event} e
             * @return {void}
             */
            drop(e) {
                let files = e.dataTransfer.files;
                
                if(this.state.isInitial() && files.length) {
                    this.addFiles(files);
                }

                this.leave();
            },

            /**
             * Select the photo.
             * 
             * @param  {Event} e
             * @return {void}
             */
            select(e) {
                this.addFiles(this.$refs.input.files);
            },

            /**
             * Add files to uploading.
             * 
             * @param {FileList} files
             */
            addFiles(files) {
                this.state.set('uploading');

                for (let i = 0; i < files.length; i++) {                
                    this.files.push(files[i]);
                }
            },

            /**
             * Listen events.
             * 
             * @return {void}
             */
            listenEvents() {
                eventDispatcher.$on('all-files-were-uploaded', () => {
                    this.state.set('done');
                });
            }
        }
    }
</script>