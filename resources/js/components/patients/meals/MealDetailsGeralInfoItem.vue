<template>
    <div class="row geral-centered">
        <div v-if="data.foodPhotoUrl != null" class="col max-w-100"
             v-on:click="() => { showZoomModal(`/food/${data.foodPhotoUrl}`) }">
            <img :src="`http://nutriclock.test:81/storage/food/${data.foodPhotoUrl}`" alt=""
                 class="img-container"/>
        </div>
        <div class="col max-w-100 pl-4" v-else>N/A</div>

        <div v-if="data.nutritionalInfoPhotoUrl != null" class="col max-w-100"
             v-on:click="() => { showZoomModal(`/nutritionalInfo/${data.nutritionalInfoPhotoUrl}`) }">
            <img :src="`http://nutriclock.test:81/storage/nutritionalInfo/${data.nutritionalInfoPhotoUrl}`"
                 alt="" class="img-container"/>
        </div>
        <div class="col max-w-100 pl-4" v-else>N/A</div>

        <div class="col ml-4">{{data.name}}</div>
        <div class="col">{{data.quantity}} {{data.relativeUnit}} - {{data.quantity * data.numericUnit}} g</div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    export default {
        props: ['data'],
        data() {
            return {
            }
        },
        methods: {
            showZoomModal(imageToZoom) {
                this.$emit('show-zoom', imageToZoom);
            },
        },
    };
</script>

<style>
    .img-container {
        height: 100px;
        width: 100px;
        object-fit: cover;
    }

    .row.geral-centered {
        display: flex;
        justify-content: center;
        align-items: center;
        color: black;
        font-size: 13px;
        font-weight: 500;
    }

    .col.max-w-100 {
        max-width: 100px;
    }
</style>
