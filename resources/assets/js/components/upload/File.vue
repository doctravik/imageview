<template>
    <div class="upload-file">
        <div class="upload-progress">
            <div class="progress__bar">
                <div class="progress__label">{{ file.name }}</div>

                <div :style="{ 'width': progress + '%' }"
                    :class="{
                        'progress__fill': true,
                        'progress__fill--success': state.isSuccess(), 
                        'progress__fill--failed': state.isFailed() || state.isCancelled() 
                    }">
                </div>

                <div class="progress__percentage">
                    <span v-if="state.isFailed()">Failed</span>
                    <span v-if="state.isSuccess()">Complete</span>
                    <span v-if="state.isCancelled()">Cancelled</span>
                    <span v-if="state.isUploading()">{{ progress }}%</span>
                </div>
            </div>

            <button class="delete upload-file__cancel" @click="cancelUpload" v-if="state.isUploading()"></button>
        </div>
        <div class="upload-file__error" v-if="errors.has(inputName)" v-text="errors.get(inputName)"></div>
    </div>
</template>

<script>
    import State from './State.js';
    import Errors from './../../utilities/Errors.js';
    let CancelToken = axios.CancelToken;

    export default {
        props: ['file', 'endpoint', 'inputName'],

        mounted() {
            this.upload();
            this.listenEvents();
        },

        data() {
            return {
                progress: 0,
                cancel: null,
                errors: new Errors(),
                state: new State(['initial', 'uploading', 'success', 'failed', 'cancelled']),
            }
        },

        methods: {
            /**
             * Upload file.
             *     
             * @return {void}
             */
            upload() {
                this.state.set('uploading');

                axios.post(this.endpoint, this.form(), this.config())
                    .then(this.onSuccess) 
                    .catch(this.onFail);
            },

            /**
             * Get form data.
             * 
             * @return {FormData}
             */
            form() {
                let form = new FormData();

                form.append(this.inputName, this.file);

                return form;
            },

            /**
             * Get request config.
             * 
             * @return {object}
             */
            config() {
                return {
                    onUploadProgress: (progressEvent) => {
                        this.progress = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                    },
                    cancelToken: new CancelToken(function executor(c) {
                        this.cancel = c;
                    }.bind(this))
                }
            },

            /**
             * Handle a successful file uploading.
             * 
             * @param {object} response
             * @return {void}
             */
            onSuccess(response) {
                eventDispatcher.$emit('file-was-uploaded', response.data.data);
                
                this.done('success');
            },

            /**
             * Handle a failed form submission.
             * 
             * @param {object} error
             * @return {void}
             */
            onFail(error) {
                if(axios.isCancel(error)) {
                    this.done('cancelled');
                } else {
                    this.errors.record(error.response.data);
                    this.done('failed');
                }
            },

            /**
             * Complete uploading.
             * 
             * @param  {string}   state
             * @return {void}
             */
            done(state) {
                this.state.set(state);
                eventDispatcher.$emit('file-was-processed');
            },

            /**
             * Cancel upload.
             * 
             * @return {void}
             */
            cancelUpload() {
                this.cancel('Operation canceled by the user.');
            },

            /**
             * Listen events.
             * 
             * @return {void}
             */
            listenEvents() {
                eventDispatcher.$on('cancel-uploading', () => {
                    this.cancelUpload();
                });
            }
        }
    }
</script>

<style scoped>
    .upload-file {
        margin: 20px;
        margin-top: 0;
    }

    .upload-file__cancel {
        margin-left: 20px;
    }

    .upload-file__error {
        font-size: .9em;
    }

    .upload-progress {
        align-items: center;
        display: flex;
        position: relative;
    }

    .progress__bar {
        background-color: #f5f5f5;
        border-radius: 3px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
        height: 35px;
        margin: 0;
        position: relative;
        width: 100%;
    }

    .progress__label,
    .progress__percentage {
        color: #333;
        margin-left: 10px;
        position: absolute;
        top: 50%;
        transform: translate(0, -50%);
    }

    .progress__percentage {
        margin-right: 10px;
        right: 0;
    }

    .progress__fill {
        background-color: #42b983;
        border-radius: 3px;
        box-shadow: inset 0 -1px rgba(0, 0, 0, .15);
        box-sizing: border-box;
        height: 100%;
        opacity: .6;
        padding: 10px;
        transition: width 500ms ease;
    }

    .progress__fill--failed {
        background-color: #f66;
        transition: none;
        width: 100%!important;
    }

    .progress__fill--success {
        opacity: 1;
    }
</style>