<template>
    <div class="file-upload-center mb-2">
        <input
            type="file"
            style="display: none"
            ref="fileInput"
            v-on:change="onFileSelected"
        />
        <img
            v-bind:src="imgUrl"
            class="profile-img"
            alt=""
            v-on:click.prevent="onFileSelect"
        />
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */

    export default{
        props:['imageUrl', 'disabled'],
        data() {
            return {
                imgUrl: null,
            };
        },
        methods:{
            onFileSelected(event) {
                const { currentTarget } = event;

                if (currentTarget && currentTarget.files) {
                    this.$emit('onFileSelected', currentTarget.files[0])
                }
            },
            onFileSelect() {
                if (this.disabled) return;
                this.$refs.fileInput.click();
            }
        },
        watch: {
            imageUrl: function(newVal, oldVal) {
                this.imgUrl = newVal;
            },
        }
    };
</script>

<style>
    .profile-img {
        height: 45px;
        width: 45px;
        object-fit: cover;
        border-radius: 50%;
        cursor: pointer;
    }

    .profile-img:hover {
        filter: grayscale(100%);
    }

    .file-upload-center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
