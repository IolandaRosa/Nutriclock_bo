<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title" id="confirmation-modal-title-span">{{title}}</span>
                    </div>
                    <div class="modal-body">
                        <p style="color: #000" id="confirmation-modal-message">{{message}}</p>
                    </div>
                    <div class="modal-footer">
                        <button :class="saveButtonClass" @click="onSaveClick" id="confirmation-modal-save-btn">
                            {{saveButtonText}}
                        </button>
                        <button class="btn-bold btn btn-secondary" @click="onCloseClick" id="confirmation-modal-close-btn">
                            {{cancelButtonText}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    export default{
        props:['title', 'message', 'saveButtonClass', 'cancelButtonText', 'saveButtonText'],
        data() {
            return {};
        },
        methods:{
            onCloseClick() {
                this.$emit('close');
            },
            onSaveClick() {
                this.$emit('save');
            },
        },
    };
</script>
