<template>
    <div class="tab-wrapper">
        <div class="container">
            <div class="with-p-4 bg-light rounded with-shadow">
                <button class="btn btn-sm btn-outline-secondary mb-4" v-on:click="this.returnToMealList">
                    🡄 Diário Alimentar
                </button>
                <button class="btn btn-dark w-100" v-on:click="() => { this.showGeralInformation = !this.showGeralInformation}">
                    <svg v-show="!showGeralInformation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    <svg v-show="showGeralInformation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                    </svg>
                    Características Gerais das Refeições de {{ this.date }}
                </button>
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

                <div v-if="this.data">
                    <button class="btn btn-dark w-100" v-on:click="() => { this.showNutritionalInformation= !this.showNutritionalInformation}">
                        <svg v-show="!showNutritionalInformation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        <svg v-show="showNutritionalInformation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                        </svg>
                        Macroconstituintes
                    </button>
                    <div v-show="showNutritionalInformation" class="table-responsive">
                        <table class="table table-hover bg-white">
                            <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Quant. (g)</th>
                                <th scope="col">kcal</th>
                                <th scope="col">Àgua (ml)</th>
                                <th scope="col">Proteína (g)</th>
                                <th scope="col">Gordura (g)</th>
                                <th scope="col">Hid. Carb. (g)</th>
                                <th scope="col">Fibra (g)</th>
                                <th scope="col">Colesterol (mg)</th>
                                <th/>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(nutri, index) in this.data['info']" :key="nutri.id">
                                <td>{{ nutri.name }}</td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="nutri.quantity"
                                        v-on:change="() => updateIQuantityValue(nutri.nutritionalInfo[0].mealId, nutri.quantity)"
                                        :disabled="readonly"
                                    >
                                </td>
                                <td v-for="n in 8">
                                    <input
                                        type="text"
                                        class="form-control"
                                        :id="nutri.nutritionalInfo[n-1].id"
                                        :name="nutri.nutritionalInfo[n-1].id"
                                        v-model="nutri.nutritionalInfo[n-1].value"
                                        v-on:change="() => updateINutritionalValue(nutri.nutritionalInfo[n-1].id, nutri.nutritionalInfo[n-1].value)"
                                        :disabled="readonly"
                                    >
                                </td>
                                <td>
                                    <button type="button" class="btn btn btn-outline-success btn-sm"
                                            v-on:click="() => updateNutritionalInfo(nutri, index)">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-clockwise"
                                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                  d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                            <path
                                                d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-dark w-100" v-on:click="() => {  this.showVitamins= !this.showVitamins}">
                        <svg v-show="!showVitamins" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        <svg v-show="showVitamins" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                        </svg>
                        Vitaminas
                    </button>
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
                                <th/>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(nutri, index) in this.data['info']" :key="nutri.id">
                                <td>{{ nutri.name }}</td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="nutri.quantity"
                                        v-on:change="() => updateIQuantityValue(nutri.nutritionalInfo[0].mealId, nutri.quantity)"
                                        :disabled="readonly"
                                    >
                                </td>
                                <td v-for="n in 15" v-if="n >= 8">
                                    <input
                                        type="text"
                                        class="form-control"
                                        :id="nutri.nutritionalInfo[n].id"
                                        :name="nutri.nutritionalInfo[n].id"
                                        v-model="nutri.nutritionalInfo[n].value"
                                        :disabled="readonly"
                                    >
                                </td>
                                <td>
                                    <button type="button" class="btn btn btn-outline-success btn-sm"
                                            v-on:click="() => updateNutritionalInfo(nutri, index)">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-clockwise"
                                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                  d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                            <path
                                                d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-dark w-100" v-on:click="() => {this.showMinerals = !this.showMinerals}">
                        <svg v-show="!showMinerals" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        <svg v-show="showMinerals" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                        </svg>
                        Minerais
                    </button>
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
                                <th/>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(nutri, index) in this.data['info']" :key="nutri.id">
                                <td>{{ nutri.name }}</td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="nutri.quantity"
                                        v-on:change="() => updateIQuantityValue(nutri.nutritionalInfo[0].mealId, nutri.quantity)"
                                        :disabled="readonly"
                                    >
                                </td>
                                <td v-for="n in 22" v-if="n >= 16">
                                    <input
                                        type="text"
                                        class="form-control"
                                        :id="nutri.nutritionalInfo[n].id"
                                        :name="nutri.nutritionalInfo[n].id"
                                        v-model="nutri.nutritionalInfo[n].value"
                                        :disabled="readonly"
                                    >
                                </td>
                                <td>
                                    <button type="button" class="btn btn btn-outline-success btn-sm"
                                            v-on:click="() => updateNutritionalInfo(nutri, index)">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-clockwise"
                                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                  d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                            <path
                                                d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <ImageZoomModal v-show="zoomModal" :image-to-show="imageToZoom" @close="closeModal"/>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import ImageZoomModal from '../../modals/ImageZoomModal';
import MealDetailsGeralInfoItem from './MealDetailsGeralInfoItem';
import {UserRoles} from "../../../constants/misc";

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
        showToast(message, messageType) {
            this.$toasted.show(message, {
                type: messageType,
                duration: 2000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
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
        updateNutritionalInfo(nutritionalInfo, index) {
            if (this.isFetching) return;

            this.isFetching = true;
            axios.put(`api/nutrititional-info/meal/${nutritionalInfo.nutritionalInfo[0].mealId}`, {
                name: nutritionalInfo.name,
                quantity: nutritionalInfo.quantity,
                nutritionalInfo: nutritionalInfo.nutritionalInfo,
            }).then(response => {
                this.isFetching = false;
                this.data.info[index].nutritionalInfo = [
                    ...response.data.data,
                ];
                this.$forceUpdate();
            }).catch((error) => {
                this.isFetching = false;
            });
        },
        updateIQuantityValue(id, value) {
            if (isNaN(value) || value < 0) {
                this.showToast('Valor inválido. Deve ser um número positivo!', 'error');
                return
            }

            if (this.isFetching) return;

            this.isFetching = true;

            axios.put(`api/meals/${id}`, {"quantity": value}).then(() => {
                this.isFetching = false;
                this.showToast('A informação foi atualizada com sucesso!', 'success');
            }).catch(() => {
                this.isFetching = false;
            });
        },
        updateINutritionalValue(id, value) {
            if (isNaN(value) || value < 0) {
                this.showToast('Valor inválido. Deve ser um número positivo!', 'error');
                return
            }

            if (this.isFetching) return;

            this.isFetching = true;

            axios.put(`api/nutritional-info/${id}`, {"value": value}).then(() => {
                this.isFetching = false;
                this.showToast('A informação foi atualizada com sucesso!', 'success');
            }).catch(() => {
                this.isFetching = false;
                this.showToast('Ocorreu um erro durante a atualização da informação', 'error');
            });
        }
    },
    mounted() {
        if (this.$store.state.user) {
            this.readonly = this.$store.state.user.role === UserRoles.Intern
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
