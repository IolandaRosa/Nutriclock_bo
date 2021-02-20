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
                <h6>Total Diário</h6>
                <div v-if="macroActive && data" class="table-responsive">
                    <table class="table table-sm bg-white">
                        <thead>
                        <tr>
                            <th scope="col">Quant.<br>(g)</th>
                            <th scope="col">Energia<br>(kcal)</th>
                            <th scope="col">Àgua<br>(ml)</th>
                            <th scope="col">Proteína<br>(g)</th>
                            <th scope="col">Gordura<br>(g)</th>
                            <th scope="col">Hid. Carb.<br>(g)</th>
                            <th scope="col">Fibra<br>(g)</th>
                            <th scope="col">Colesterol<br>(mg)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ data.total.quant }}</td>
                            <td>{{ data.total.energy }}</td>
                            <td>{{ data.total.water }}</td>
                            <td>{{ data.total.protein }}</td>
                            <td>{{ data.total.fat }}</td>
                            <td>{{ data.total.carbs }}</td>
                            <td>{{ data.total.fiber }}</td>
                            <td>{{ data.total.coletrol }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="vitActive && data" class="table-responsive">
                    <table class="table table-sm bg-white">
                        <thead>
                        <tr>
                            <th scope="col">Quant.<br>(g)</th>
                            <th scope="col">Vit A<br>(mg)</th>
                            <th scope="col">Vit D<br>(μg)</th>
                            <th scope="col">Tiamina<br>(mg)</th>
                            <th scope="col">Riboflavina<br>(mg)</th>
                            <th scope="col">Niacina<br>(mg)</th>
                            <th scope="col">Vit B6<br>(mg)</th>
                            <th scope="col">Vit B12<br>(μg)</th>
                            <th scope="col">Vit C<br>(mg)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ data.total.quant }}</td>
                            <td>{{ data.total.A }}</td>
                            <td>{{ data.total.D }}</td>
                            <td>{{ data.total.tiamina }}</td>
                            <td>{{ data.total.riboflavina }}</td>
                            <td>{{ data.total.niacina }}</td>
                            <td>{{ data.total.B6 }}</td>
                            <td>{{ data.total.B12 }}</td>
                            <td>{{ data.total.C }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="minActive && data" class="table-responsive">
                    <table class="table table-sm bg-white">
                        <thead>
                        <tr>
                            <th scope="col">Quant.<br>(g)</th>
                            <th scope="col">Sódio<br>(mg)</th>
                            <th scope="col">Potássio<br>(mg)</th>
                            <th scope="col">Cálcio<br>(mg)</th>
                            <th scope="col">Fósforo<br>(mg)</th>
                            <th scope="col">Magnésio<br>(mg)</th>
                            <th scope="col">Ferro<br>(mg)</th>
                            <th scope="col">Zinco<br>(mg)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ data.total.quant }}</td>
                            <td>{{ data.total.Na }}</td>
                            <td>{{ data.total.K }}</td>
                            <td>{{ data.total.Ca }}</td>
                            <td>{{ data.total.P }}</td>
                            <td>{{ data.total.Mg }}</td>
                            <td>{{ data.total.Fe }}</td>
                            <td>{{ data.total.Zn }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btn-group btn-group-sm w-100 mb-2" style="z-index: 0 !important;" role="group">
                    <button :class="macroActive ? 'btn btn-dark w-30' : 'btn btn-secondary w-30'"
                            v-on:click.prevent="() => {
                                macroActive = true;
                                vitActive = false;
                                minActive = false;
                            }">
                        Macroconstituintes
                    </button>
                    <button :class="vitActive ? 'btn btn-dark w-30' : 'btn btn-secondary w-30'"
                            v-on:click.prevent="() => {
                                macroActive = false;
                                vitActive = true;
                                minActive = false;
                            }">
                        Vitaminas
                    </button>
                    <button :class="minActive ? 'btn btn-dark w-30' : 'btn btn-secondary w-30'"
                            v-on:click.prevent="() => {
                                macroActive = false;
                                vitActive = false;
                                minActive = true;
                            }">
                        Minerais
                    </button>
                </div>
                <div v-show="macroActive">
                    <div v-if="data && data['P'].length > 0">
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
                    <div v-if="data && data['M'].length > 0">
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
                    <div v-if="data && data['A'].length > 0">
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
                    <div v-if="data && data['L'].length > 0">
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
                    <div v-if="data && data['J'].length > 0">
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
                    <div v-if="data && data['O'].length > 0">
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
                    <div v-if="data && data['S'].length > 0">
                        <DetailsButton title="Snacks" type="SO" @update-show="updateShow"/>
                        <MacroNutrientsTable
                            v-show="showMacroNight"
                            :data="data['S']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'S')"
                        />
                    </div>
                </div>
                <div class="mt-2" v-show="vitActive">
                    <div v-if="data && data['P'].length > 0">
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
                    <div v-if="data && data['M'].length > 0">
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
                    <div v-if="data && data['A'].length > 0">
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
                    <div v-if="data && data['L'].length > 0">
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
                    <div v-if="data && data['J'].length > 0">
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
                    <div v-if="data && data['O'].length > 0">
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
                    <div v-if="data && data['S'].length > 0">
                        <DetailsButton title="Snacks" type="VS" @update-show="updateShow"/>
                        <VitaminsTable
                            v-show="showVitNight"
                            :data="data['S']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'S')"
                        />
                    </div>
                </div>
                <div class="mt-2" v-show="minActive">
                    <div v-if="data && data['P'].length > 0">
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
                    <div v-if="data && data['M'].length > 0">
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
                    <div v-if="data && data['A'].length > 0">
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
                    <div v-if="data && data['L'].length > 0">
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
                    <div v-if="data && data['J'].length > 0">
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
                    <div v-if="data && data['O'].length > 0">
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
                    <div v-if="data && data['S'].length > 0">
                        <DetailsButton title="Snacks" type="MiS" @update-show="updateShow"/>
                        <MineralsTable
                            v-show="showMinNight"
                            :data="data['S']"
                            @show-zoom-modal="showZoomModal"
                            @update-quantity-value="updateQuantityValue"
                            @update-nutritional-value="updateNutritionalValue"
                            @update-nutritional-info="(info, index) => updateNutritionalInfo(info, index, 'S')"
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
                this.computeSubtotals();
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
                this.computeSubtotals();
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
                this.computeSubtotals();
            }).catch(() => {
                this.isFetching = false;
                this.showToast('Ocorreu um erro durante a atualização da informação', 'error');
            });
        },
        setAltImage(event) {
            event.target.src = '/images/avatar.jpg'
        },
        computeSum(value, object, index) {
            console.log(Number(object.nutritionalInfo[index].value))
            console.log(Number(value))
            return (Number(value) + Number(object.nutritionalInfo[index].value)).toFixed(2);
        },
        computeSumTotal(value1, value2) {
            return (Number(value1) + Number(value2)).toFixed(2);
        },
        computeSubtotals() {
            const totals = {
                quant: 0,
                energy: 0,
                water: 0,
                protein: 0,
                fat: 0,
                carbs: 0,
                fiber: 0,
                coletrol: 0,
                A: 0,
                D: 0,
                tiamina: 0,
                riboflavina: 0,
                niacina: 0,
                B6: 0,
                B12: 0,
                C: 0,
                Na: 0,
                K: 0,
                Ca: 0,
                P: 0,
                Mg: 0,
                Fe: 0,
                Zn: 0,
            };

            Object.keys(this.data).forEach(key => {
                const subtotals = {
                    sumQuant: 0,
                    sumEnergy: 0,
                    sumWater: 0,
                    sumProtein: 0,
                    sumFat: 0,
                    sumCarbs: 0,
                    sumFiber: 0,
                    sumColetrol: 0,
                    sumA: 0,
                    sumD: 0,
                    sumTiamina: 0,
                    sumRiboflavina: 0,
                    sumNiacina: 0,
                    sumB6: 0,
                    sumB12: 0,
                    sumC: 0,
                    sumNa: 0,
                    sumK: 0,
                    sumCa: 0,
                    sumP: 0,
                    sumMg: 0,
                    sumFe: 0,
                    sumZn: 0,
                };

                this.data[key].forEach(object => {
                    subtotals.sumQuant = (Number(subtotals.sumQuant) + Number(object.meal.numericUnit)).toFixed(2);
                    subtotals.sumEnergy = this.computeSum(subtotals.sumEnergy, object, 0);
                    subtotals.sumWater = this.computeSum(subtotals.sumWater, object, 2);
                    subtotals.sumProtein = this.computeSum(subtotals.sumProtein, object, 3);
                    subtotals.sumFat = this.computeSum(subtotals.sumFat, object, 4);
                    subtotals.sumCarbs = this.computeSum(subtotals.sumCarbs, object, 5);
                    subtotals.sumFiber = this.computeSum(subtotals.sumFiber, object, 6);
                    subtotals.sumColetrol = this.computeSum(subtotals.sumColetrol, object, 7);
                    subtotals.sumA = this.computeSum(subtotals.sumA, object, 8);
                    subtotals.sumD = this.computeSum(subtotals.sumD, object, 9);
                    subtotals.sumTiamina = this.computeSum(subtotals.sumTiamina, object, 10);
                    subtotals.sumRiboflavina = this.computeSum(subtotals.sumRiboflavina, object, 11);
                    subtotals.sumNiacina = this.computeSum(subtotals.sumNiacina, object, 12);
                    subtotals.sumB6 = this.computeSum(subtotals.sumB6, object, 13);
                    subtotals.sumB12 = this.computeSum(subtotals.sumB12, object, 14);
                    subtotals.sumC = this.computeSum(subtotals.sumC, object, 15);
                    subtotals.sumNa = this.computeSum(subtotals.sumNa, object, 16);
                    subtotals.sumK = this.computeSum(subtotals.sumK, object, 17);
                    subtotals.sumCa = this.computeSum(subtotals.sumCa, object, 18);
                    subtotals.sumP = this.computeSum(subtotals.sumP, object, 19);
                    subtotals.sumMg = this.computeSum(subtotals.sumMg, object, 20);
                    subtotals.sumFe = this.computeSum(subtotals.sumFe, object, 21);
                    subtotals.sumZn = this.computeSum(subtotals.sumZn, object, 22);
                });

                totals.quant = (Number(totals.quant) + Number(subtotals.sumQuant)).toFixed(2);
                totals.energy = this.computeSumTotal(totals.energy, subtotals.sumEnergy);
                totals.water = this.computeSumTotal(totals.water, subtotals.sumWater);
                totals.protein = this.computeSumTotal(totals.protein, subtotals.sumProtein);
                totals.fat = this.computeSumTotal(totals.fat, subtotals.sumFat);
                totals.carbs = this.computeSumTotal(totals.carbs, subtotals.sumCarbs);
                totals.fiber = this.computeSumTotal(totals.fiber, subtotals.sumFiber);
                totals.coletrol = this.computeSumTotal(totals.coletrol, subtotals.sumColetrol);
                totals.A = this.computeSumTotal(totals.A, subtotals.sumA);
                totals.D = this.computeSumTotal(totals.D, subtotals.sumD);
                totals.tiamina = this.computeSumTotal(totals.tiamina, subtotals.sumTiamina);
                totals.riboflavina = this.computeSumTotal(totals.riboflavina, subtotals.sumRiboflavina);
                totals.niacina = this.computeSumTotal(totals.niacina, subtotals.sumNiacina);
                totals.B6 = this.computeSumTotal(totals.B6, subtotals.sumB6);
                totals.B12 = this.computeSumTotal(totals.B12, subtotals.sumB12);
                totals.C = this.computeSumTotal(totals.C, subtotals.sumC);
                totals.Na = this.computeSumTotal(totals.Na, subtotals.sumNa);
                totals.K = this.computeSumTotal(totals.K, subtotals.sumK);
                totals.Ca = this.computeSumTotal(totals.Ca, subtotals.sumCa);
                totals.P = this.computeSumTotal(totals.P, subtotals.sumP);
                totals.Mg = this.computeSumTotal(totals.Mg, subtotals.sumMg);
                totals.Fe = this.computeSumTotal(totals.Fe, subtotals.sumFe);
                totals.Zn = this.computeSumTotal(totals.Zn, subtotals.sumZn);

                this.data[key].subtotals = subtotals;
            });

            this.data = {
                ...this.data,
                total: totals,
            }
        }
    },
    watch: {
        meal: function (newVal, oldVal) {
            this.data = newVal;
            if (this.data) this.computeSubtotals();
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
