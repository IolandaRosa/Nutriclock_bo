<template>
    <div>
        <input
            type="file"
            style="display: none"
            ref="fileInput"
            v-on:change="onFileSelected"
        />
        <img
            v-bind:src="imgUrl"
            :class="disabled ? 'profile-img' : 'profile-img pointer'"
            alt=""
            v-on:click.prevent="onFileSelect"
            @error="setAltImage"
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
            },
            setAltImage (event) {
                event.target.src = "/images/avatar.jpg"
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
        height: 100px;
        width: 100px;
        object-fit: cover;
        border-radius: 50%;
    }

    @media only screen and (max-width: 600px) {
        .profile-img {
            height: 60px;
            width: 60px;
            margin-right: 16px;
        }
    }

    .profile-img:hover {
        filter: grayscale(100%);
    }
</style>
