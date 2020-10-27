<template>
    <div class="tab-wrapper">
        <p class="return" v-on:click="this.returnToMealList"><strong>< Retroceder para Diário de Refeições</strong></p>
        <div class="component-wrapper-header">
            <div class="component-wrapper-left">
                {{this.data.name}}
            </div>
        </div>
        <div class="component-wrapper-body">
            <div class="row">
                <div v-show="this.data.foodPhotoUrl != null" class="col-md-3" v-on:click="() => { showZoomModal(`/food/${this.data.foodPhotoUrl}`) }">
                    <img :src="`http://nutriclock.test:81/storage/food/${this.data.foodPhotoUrl}`" alt="" class="img-container" />
                </div>
                <div v-show="this.data.nutritionalInfoPhotoUrl != null" class="col-md-9" v-on:click="() => { showZoomModal(`/nutritionalInfo/${this.data.nutritionalInfoPhotoUrl}`) }">
                    <img :src="`http://nutriclock.test:81/storage/nutritionalInfo/${this.data.nutritionalInfoPhotoUrl}`" alt="" class="img-container" />
                </div>
            </div>
            <div class="row">
                <span class="text-secondary label-small col-md-12">Registado a {{this.data.parsedDate}} pelas {{this.data.hour}} horas</span>
            </div>
            <div class="row">
                <span class="text-secondary label-small col-md-12">Tipo de Refeição: {{this.data.parsedType}}</span>
            </div>
            <div class="row">
                <span class="text-secondary label-small col-md-12">Quantidate: {{this.data.parsedQuantity}}</span>
            </div>
            <div class="row">
                <span class="text-secondary label-small col-md-12">Informação Nutricional</span>
            </div>
        </div>
        <ImageZoomModal v-show="zoomModal" :image-to-show="imageToZoom" @close="closeModal"/>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import ImageZoomModal from '../../modals/ImageZoomModal';

    export default{
        props: ['meal'],
        data() {
            return {
                isFetching: false,
                data: null,
                zoomModal: false,
                imageToZoom: '',
            }
        },
        methods: {
            returnToMealList () {
                this.$emit('close-details');
            },
            showZoomModal(imageToZoom) {
                this.zoomModal = true;
                this.imageToZoom = imageToZoom;
            },
            closeModal(){
                this.zoomModal = false;
                this.imageToZoom = '';
            }
        },
        watch: {
            meal: function (newVal, oldVal) {
                this.data = newVal;
            },
        },
        components: {
            ImageZoomModal,
        }
    };
</script>

<style>
    .return {
        color: grey;
        font-size: 12px;
        cursor: pointer;
        font-weight: 700;
    }

    .img-container {
        height: 200px;
        width: 100%;
        object-fit: cover;
    }
</style>
