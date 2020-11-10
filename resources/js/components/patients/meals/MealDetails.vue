<template>
    <div class="tab-wrapper">
        <p class="return" v-on:click="this.returnToMealList"><strong>< Retroceder para Diário Alimentar</strong></p>
        <div class="meal-detail-title">
            <span class="flex-grow-1">Características Gerais das Refeições de {{this.date}}</span>
            <span class="px-4" style="cursor: pointer;"
                  v-on:click="() => { this.showGeralInformation = !this.showGeralInformation}">{{showGeralInformation ? '-': '+'}}</span>
        </div>
        <div v-show="showGeralInformation" v-if="this.data">
            <div v-show="this.data['P'].length > 0" class="meal-detail-geral-container">
                <div class="meal-detail-geral-container-title">Pequeno Almoço</div>
                <div v-for="m in this.data['P']" :key="m.id">
                    <MealDetailsGeralInfoItem
                        :data="m"
                        @show-zoom="showZoomModal"/>
                </div>
            </div>
            <div v-show="this.data['A'].length > 0" class="meal-detail-geral-container">
                <div class="meal-detail-geral-container-title">Almoço</div>
                <div v-for="m in this.data['A']" :key="m.id">
                    <MealDetailsGeralInfoItem
                        :data="m"
                        @show-zoom="showZoomModal"/>
                </div>
            </div>

            <div v-show="this.data['L'].length > 0" class="meal-detail-geral-container">
                <div class="meal-detail-geral-container-title">Lanche</div>
                <div v-for="m in this.data['L']" :key="m.id">
                    <MealDetailsGeralInfoItem
                        :data="m"
                        @show-zoom="showZoomModal"/>
                </div>
            </div>

            <div v-show="this.data['J'].length > 0" class="meal-detail-geral-container">
                <div class="meal-detail-geral-container-title">Jantar</div>
                <div v-for="m in this.data['J']" :key="m.id">
                    <MealDetailsGeralInfoItem
                        :data="m"
                        @show-zoom="showZoomModal"/>
                </div>
            </div>

            <div v-show="this.data['S'].length > 0" class="meal-detail-geral-container">
                <div class="meal-detail-geral-container-title">Snacks</div>
                <div v-for="m in this.data['S']" :key="m.id">
                    <MealDetailsGeralInfoItem
                        :data="m"
                        @show-zoom="showZoomModal"/>
                </div>
            </div>
        </div>

        <div class="meal-detail-simple-title">
            Informação Nutricional
        </div>

        <div v-if="this.data">
            <div class="meal-detail-title mt-4">
                <span class="flex-grow-1">Macroconstituintes</span>
                <span class="px-4" style="cursor: pointer;"
                      v-on:click="() => { this.showNutritionalInformation= !this.showNutritionalInformation}">{{showNutritionalInformation ? '-': '+'}}</span>
            </div>

            <div v-show="showNutritionalInformation" class="table-responsive">
                <table class="table table-hover bg-white">
                    <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Quant. (g)</th>
                        <th scope="col">kcal</th>
                        <th scope="col">Àgua (g)</th>
                        <th scope="col">Proteína (g)</th>
                        <th scope="col">Gordura (g)</th>
                        <th scope="col">Hid. Carb. (g)</th>
                        <th scope="col">Fibra (g)</th>
                        <th scope="col">Colesterol (mg)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="nutri in this.data['info']" :key="nutri.id">
                        <td>{{nutri.name}}</td>
                        <td>{{nutri.quantity}}</td>
                        <td v-for="n in 7">
                            <input
                                type="text"
                                class="form-control"
                                :id="nutri.nutritionalInfo[n].id"
                                :name="nutri.nutritionalInfo[n].id"
                                v-model="nutri.nutritionalInfo[n].value"
                                v-on:change="() => updateINutritionalValue(nutri.nutritionalInfo[n].id, nutri.nutritionalInfo[n].value)"
                            >
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="meal-detail-title mt-4">
                <span class="flex-grow-1">Vitaminas</span>
                <span class="px-4" style="cursor: pointer;"
                      v-on:click="() => { this.showVitamins= !this.showVitamins}">{{showVitamins ? '-': '+'}}</span>
            </div>
            <div v-show="showVitamins" class="table-responsive">
                <table class="table table-hover bg-white">
                    <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Quant. (g)</th>
                        <th scope="col">Vit A (mg)</th>
                        <th scope="col">Vit D (μg)</th>
                        <th scope="col">Tiamina (mg)</th>
                        <th scope="col">Riboflavina (mg)</th>
                        <th scope="col">Niacina (mg)</th>
                        <th scope="col">Vit B6 (mg)</th>
                        <th scope="col">Vit B12 (μg)</th>
                        <th scope="col">Vit C (mg)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="nutri in this.data['info']" :key="nutri.id">
                        <td>{{nutri.name}}</td>
                        <td>{{nutri.quantity}}</td>
                        <td v-for="n in 15" v-if="n >= 8">
                            <input
                                type="text"
                                class="form-control"
                                :id="nutri.nutritionalInfo[n].id"
                                :name="nutri.nutritionalInfo[n].id"
                                v-model="nutri.nutritionalInfo[n].value"
                            >
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="meal-detail-title mt-4">
                <span class="flex-grow-1">Minerais</span>
                <span class="px-4" style="cursor: pointer;"
                      v-on:click="() => { this.showMinerals = !this.showMinerals}">{{showMinerals ? '-': '+'}}</span>
            </div>
            <div v-show="showMinerals" class="table-responsive">
                <table class="table table-hover bg-white">
                    <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Quant. (g)</th>
                        <th scope="col">Sódio (mg)</th>
                        <th scope="col">Potássio (mg)</th>
                        <th scope="col">Cálcio (mg)</th>
                        <th scope="col">Fósforo (mg)</th>
                        <th scope="col">Magnésio (mg)</th>
                        <th scope="col">Ferro (mg)</th>
                        <th scope="col">Zinco (mg)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="nutri in this.data['info']" :key="nutri.id">
                        <td>{{nutri.name}}</td>
                        <td>{{nutri.quantity}}</td>
                        <td v-for="n in 22" v-if="n >= 16">
                            <input
                                type="text"
                                class="form-control"
                                :id="nutri.nutritionalInfo[n].id"
                                :name="nutri.nutritionalInfo[n].id"
                                v-model="nutri.nutritionalInfo[n].value"
                            >
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <ImageZoomModal v-show="zoomModal" :image-to-show="imageToZoom" @close="closeModal"/>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import ImageZoomModal from '../../modals/ImageZoomModal';
    import MealDetailsGeralInfoItem from './MealDetailsGeralInfoItem';

    export default {
        props: ['meal', 'date'],
        data() {
            return {
                isFetching: false,
                data: null,
                zoomModal: false,
                imageToZoom: '',
                showGeralInformation: true,
                showNutritionalInformation: true,
                showVitamins: true,
                showMinerals: true,
                readonly: false,
            }
        },
        methods: {
            returnToMealList() {
                this.$emit('close-details');
            },
            showZoomModal(imageToZoom) {
                this.zoomModal = true;
                this.imageToZoom = imageToZoom;
            },
            closeModal() {
                this.zoomModal = false;
                this.imageToZoom = '';
            },
            updateINutritionalValue(id, value) {
                if (isNaN(value) || value < 0) {
                    this.$toasted.show('Valor inválido. Deve ser um número positivo!', {
                        type: 'error',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                    return
                }

                axios.put(`api/nutritional-info/${id}`, { "value": value }).then(() =>{
                    this.$toasted.show('A informação foi atualizada com sucesso!', {
                        type: 'success',
                        duration: 2000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                }).catch(() => {
                    this.$toasted.show('Ocorreu um erro durante a atualização da informação', {
                        type: 'error',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                });
            }
        },
        watch: {
            meal: function (newVal, oldVal) {
                this.data = newVal;
            },
            date: function (newVal, oldVal) {
                this.date = newVal;
            },
        },
        components: {
            MealDetailsGeralInfoItem,
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

    .meal-detail-title {
        background: #FFFFFF90;
        color: green;
        font-size: 16px;
        font-weight: bolder;
        font-family: "Nunito", sans-serif;
        padding: 8px;
        border-radius: 4px;
        margin-bottom: 16px;
        display: flex;
    }

    .meal-detail-geral-container {
        background: #FFFFFF;
        color: dimgrey;
        margin-bottom: 32px;
    }

    .meal-detail-geral-container-title {
        padding: 8px 8px 4px;
        border-bottom: 2px solid lightgray;
        color: black;
        font-size: 14px;
        font-weight: 900;
    }

    .meal-detail-simple-title {
        font-size: 14px;
        color: green;
        font-weight: 900;
        margin-top: 48px;
    }
</style>
