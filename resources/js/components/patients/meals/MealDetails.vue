<template>
    <div class="tab-wrapper">
        <div class="container text-dark">
            <div class="with-p-4 bg-light rounded with-shadow align-items-end">
                <div class="d-flex w-100 d-column">
                    <button class="btn btn-sm btn-outline-secondary mb-4 mr-auto" v-on:click="this.returnToMealList">
                        🡄 Diário Alimentar
                    </button>
                    <h5>
                        Informação Nutricional {{ this.date }}
                    </h5>
                </div>
                <div class="btn-group btn-group-sm w-100" style="z-index: 0 !important;" role="group">
                    <button :class="macroActive ? 'btn btn-primary w-30' : 'btn btn-outline-primary w-30'"
                            v-on:click.prevent="() => {
                                macroActive = true;
                                vitActive = false;
                                minActive = false;
                            }">
                        Macroconstituintes
                    </button>
                    <button :class="vitActive ? 'btn btn-primary w-30' : 'btn btn-outline-primary w-30'"
                            v-on:click.prevent="() => {
                                macroActive = false;
                                vitActive = true;
                                minActive = false;
                            }">
                        Vitaminas
                    </button>
                    <button :class="minActive ? 'btn btn-primary w-30' : 'btn btn-outline-primary w-30'"
                            v-on:click.prevent="() => {
                                macroActive = false;
                                vitActive = false;
                                minActive = true;
                            }">
                        Minerais
                    </button>
                </div>
                <div v-show="macroActive">
                    <div v-if="data['P'].length > 0">
                        <DetailsButton title="Pequeno-almoço" type="MP" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroBreakfast"
                            :data="data['P']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'P')"
                        />
                    </div>
                    <div v-if="data['M'].length > 0">
                        <DetailsButton title="Meio da manhã" type="MM" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroMidDay"
                            :data="data['M']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'M')"
                        />
                    </div>
                    <div v-if="data['A'].length > 0">
                        <DetailsButton title="Almoço" type="MA" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroLunch"
                            :data="data['A']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'A')"
                        />
                    </div>
                    <div v-if="data['L'].length > 0">
                        <DetailsButton title="Lanche" type="ML" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroMidAfternoon"
                            :data="data['L']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'L')"
                        />
                    </div>
                    <div v-if="data['J'].length > 0">
                        <DetailsButton title="Jantar" type="MJ" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroDinner"
                            :data="data['J']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'J')"
                        />
                    </div>
                    <div v-if="data['O'].length > 0">
                        <DetailsButton title="Ceia" type="MO" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroNight"
                            :data="data['O']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'O')"
                        />
                    </div>
                </div>
                <div class="mt-2" v-show="vitActive">
                    <div v-if="data['P'].length > 0">
                        <DetailsButton title="Pequeno-almoço" type="VP" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitBreakfast"
                            :data="data['P']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'P')"
                        />
                    </div>
                    <div v-if="data['M'].length > 0">
                        <DetailsButton title="Meio da manhã" type="VM" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitMidDay"
                            :data="data['M']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'M')"
                        />
                    </div>
                    <div v-if="data['A'].length > 0">
                        <DetailsButton title="Almoço" type="VA" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitLunch"
                            :data="data['A']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'A')"
                        />
                    </div>
                    <div v-if="data['L'].length > 0">
                        <DetailsButton title="Lanche" type="VL" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitMidAfternoon"
                            :data="data['L']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'L')"
                        />
                    </div>
                    <div v-if="data['J'].length > 0">
                        <DetailsButton title="Jantar" type="VJ" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitDinner"
                            :data="data['J']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'J')"
                        />
                    </div>
                    <div v-if="data['O'].length > 0">
                        <DetailsButton title="Ceia" type="VO" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitNight"
                            :data="data['O']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'O')"
                        />
                    </div>
                </div>
                <div class="mt-2" v-show="minActive">
                    <div v-if="data['P'].length > 0">
                        <DetailsButton title="Pequeno-almoço" type="MiP" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinBreakfast"
                            :data="data['P']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'P')"
                        />
                    </div>
                    <div v-if="data['M'].length > 0">
                        <DetailsButton title="Meio da manhã" type="MiM" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinMidDay"
                            :data="data['M']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'M')"
                        />
                    </div>
                    <div v-if="data['A'].length > 0">
                        <DetailsButton title="Almoço" type="MiA" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinLunch"
                            :data="data['A']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'A')"
                        />
                    </div>
                    <div v-if="data['L'].length > 0">
                        <DetailsButton title="Lanche" type="MiL" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinMidAfternoon"
                            :data="data['L']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'L')"
                        />
                    </div>
                    <div v-if="data['J'].length > 0">
                        <DetailsButton title="Jantar" type="MiJ" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinDinner"
                            :data="data['J']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'J')"
                        />
                    </div>
                    <div v-if="data['O'].length > 0">
                        <DetailsButton title="Ceia" type="MiO" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinNight"
                            :data="data['O']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'O')"
                        />
                    </div>
                </div>
            </div>
        </div>
        <ImageZoomModal v-show="zoomModal" :image-to-show="imageToZoom" @close="closeModal"/>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import DetailsButton from './DetailsButton';
import MacroNutrientsTable from './MacroNutrientsTable';
import VitaminsTable from './VitaminsTable';
import MineralsTable from './MineralsTable';
import ImageZoomModal from '../../modals/ImageZoomModal';
import MealDetailsGeralInfoItem from './MealDetailsGeralInfoItem';

export default {
    props: ['meal', 'date'],
    data() {
        return {
            isFetching: false,
            data: null,
            showMacroBreakfast: true,
            showMacroMidDay: true,
            showMacroLunch: true,
            showMacroMidAfternoon: true,
            showMacroDinner: true,
            showMacroNight: true,
            showVitBreakfast: true,
            showVitMidDay: true,
            showVitLunch: true,
            showVitMidAfternoon: true,
            showVitDinner: true,
            showVitNight: true,
            showMinBreakfast: true,
            showMinMidDay: true,
            showMinLunch: true,
            showMinMidAfternoon: true,
            showMinDinner: true,
            showMinNight: true,
            zoomModal: false,
            imageToZoom: '',
            macroActive: true,
            vitActive: false,
            minActive: false
        }
    },
    methods: {
        updateShow(value, type) {
            switch (type) {
                case 'MP':
                    this.showMacroBreakfast = value;
                    break;
                case 'MM':
                    this.showMacroMidDay = value;
                    break;
                case 'MA':
                    this.showMacroLunch = value;
                    break;
                case 'ML':
                    this.showMacroMidAfternoon = value;
                    break;
                case 'MJ':
                    this.showMacroDinner = value;
                    break;
                case 'MO':
                    this.showMacroNight = value;
                    break;
                case 'VA':
                    this.showVitLunch = value;
                    break;
                case 'VL':
                    this.showVitMidAfternoon = value;
                    break;
                case 'VP':
                    this.showVitBreakfast = value;
                    break;
                case 'VM':
                    this.showVitMidDay = value;
                    break;
                case 'VJ':
                    this.showVitDinner = value;
                    break;
                case 'VO':
                    this.showVitNight = value;
                    break;
                case 'MiP':
                    this.showMinBreakfast = value;
                    break;
                case 'MiM':
                    this.showMinMidDay = value;
                    break;
                case 'MiA':
                    this.showMinLunch = value;
                    break;
                case 'MiL':
                    this.showMinMidAfternoon = value;
                    break;
                case 'MiJ':
                    this.showMinDinner = value;
                    break;
                case 'MiO':
                    this.showMinNight = value;
                    break;
            }
        },
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
        updateNutritionalInfo(nutritionalInfo, index, type) {
            if (this.isFetching) return;

            this.isFetching = true;
            axios.put(`api/nutrititional-info/meal/${this.data[type][index].meal.id}`, {
                name: this.data[type][index].meal.name,
                quantity: this.data[type][index].meal.numericUnit,
                nutritionalInfo: this.data[type][index].nutritionalInfo,
            }).then(response => {
                this.isFetching = false;
                this.data[type][index].nutritionalInfo = [
                    ...response.data.data,
                ];
                this.$forceUpdate();
            }).catch((error) => {
                this.isFetching = false;
            });
        },
        updateQuantityValue(id, value) {
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
        updateNutritionalValue(id, value) {
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
        },
        setAltImage(event) {
            event.target.src = '/images/avatar.jpg'
        }
    },
    watch: {
        meal: function (newVal, oldVal) {
            this.data = newVal;
            console.log(this.data);
        },
        date: function (newVal, oldVal) {
            this.date = newVal;
        },
    },
    components: {
        VitaminsTable,
        MineralsTable,
        DetailsButton,
        MealDetailsGeralInfoItem,
        ImageZoomModal,
        MacroNutrientsTable,
    }
};
</script>

<style>
@media only screen and (max-width: 600px) {
    .d-column {
        flex-direction: column;
    }
}

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
.btn-group > .btn:hover, .btn-group-vertical > .btn:hover .btn:focus-visible {
    z-index: 0 !important;
}
</style>
