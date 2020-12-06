<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container zoom-container">
                    <div class="modal-body zoom-body">
                        <button class="btn-bold btn btn-light" @click="onCloseClick" style="position: absolute; right: 0;">
                            X
                        </button>
                        <img class="image-modal-class" :src="`https://nutriclock.s3-eu-west-1.amazonaws.com${imageToShow}`" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */

    export default{
        props:['imageToShow'],
        methods:{
            onCloseClick() {
                this.$emit('close');
            },
        },
        watch: {
            imageToShow: function(newVal, oldVal) {
                this.imageToShow = newVal;
            },
        },
    };
</script>
<style>
    .zoom-container {
        width: unset;
        max-width: unset;
        overflow: auto;
    }

    .zoom-body {
        display: flex;
        justify-content: center;
    }

    .image-modal-class {
        max-width: 100%;
    }
</style>
