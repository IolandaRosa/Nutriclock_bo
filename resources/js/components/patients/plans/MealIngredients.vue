<template>
    <div class="tab-wrapper">
        <div class="container with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h5 class="component-wrapper-left text-secondary d-flex align-items-center"
                        style="font-size: 18px !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-calendar mr-2" viewBox="0 0 16 16">
                            <path
                                d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        {{ dateString }}
                    </h5>
                    <div class="component-wrapper-right d-flex flex-column">
                        <button class="btn-bold btn btn-primary mb-1" data-toggle="tooltip" title="Adicionar Refeição"
                                type="button" v-on:click.prevent="openMealTypeModal">
                            <span v-if="isFetching" aria-hidden="true" class="spinner-border spinner-border-sm"
                                  role="status"></span>Adicionar Refeição
                        </button>
                        <button class="btn-bold btn btn-primary" data-toggle="tooltip" title="Guardar"
                                type="button" v-on:click.prevent="saveMeal">
                            <span v-if="isFetching" aria-hidden="true" class="spinner-border spinner-border-sm"
                                  role="status"></span>
                            Guardar
                        </button>
                    </div>
                </div>
                <div class="component-wrapper-body mt-2 text-dark table-wrapper rounded p-3 d-flex break-column">
                    <div class="bg-light rounded with-shadow p-2 w-50 w-all">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Pesquisar..."
                                   aria-describedby="basic-addon2" v-model="searchInput">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" @click="searchIngredients">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="font-weight-bold mb-2">
                            Ingredientes disponíveis
                        </div>
                        <table class="table table-sm table-striped bg-white table-hover">
                            <tbody>
                            <tr :key="index" v-for="(ing, index) in ingredients">
                                <td>{{ ing.name }}</td>
                                <td v-on:click="() => { addIngredientToActiveMeal(ing) }"
                                    class="btn text-primary btn-link text-right w-100">Adicionar
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <div :key="index" v-for="(meal, index) in meals"
                             class="bg-light rounded with-shadow p-2 ml-2 mb-2">
                            <div class="d-flex align-items-center text-secondary font-weight-bold">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24"
                                             width="20">
                                            <path d="M0 0h24v24H0z" fill="none"/>
                                            <path fill="#6c757d"
                                                  d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                                        </svg>
                                        <span class="ml-2">{{ renderMealName(meal.name) }}</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" class="bi bi-clock text-secondary mr-1"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                            <path
                                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                        </svg>
                                        <span style="font-size: 14px" class="mr-4">{{ meal.time }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" class="bi bi-people text-secondary mr-1"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                        </svg>
                                        <span style="font-size: 14px">{{ renderPortion(meal.portion) }}</span>
                                    </div>
                                    <div class="btn btn-link pl-0">
                                        Consultar Informação Nutricional
                                    </div>
                                </div>
                                <div>
                                    <span @click="() => { selectMealByIndex(index)}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" class="bi bi-pencil-fill text-info mr-2"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                        </svg>
                                    </span>
                                    <span @click="() => { removeMealByIndex(index)}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" class="bi bi-trash-fill text-danger"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div v-show="index === selectedMealIndex">
                                <div v-if="!meal.ingredients || meal.ingredients.length === 0">
                                    Adicionar ingredientes
                                </div>
                                <div v-else :key="index" v-for="(i, index) in meal.ingredients"
                                     class="rounded bg-white with-shadow p-2 mb-2">
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1">
                                            {{ i.name }}
                                        </div>
                                        <div @click="() => { removeIngredient(index) }">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-x-square-fill text-danger"
                                                 viewBox="0 0 16 16">
                                                <path
                                                    d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Quantidade:</label>
                                        <div class="col-sm-3">
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-bind:class="{ 'is-invalid': i.errors.quantity !== null }"
                                                v-model.trim="i.quantity"
                                            >
                                            <div v-if="i.errors.quantity" class="invalid-feedback">
                                                {{ i.errors.quantity }}
                                            </div>
                                        </div>
                                        <div class="col-sm-5">

                                            <select
                                                    class="form-control mb-2"
                                                    style="height: 40px"
                                                    v-bind:class="{ 'is-invalid': i.errors.unit !== null }"
                                                    v-model.trim="i.unit">
                                                    <option :key="index" v-for="(i, index) in getMealTypeOptions()" :value="i.value">
                                                        {{ i.label }}</option>
                                            </select>

                                            <div v-if="i.errors.unit" class="invalid-feedback">
                                                {{ i.errors.unit }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <MealTypeModal @close="closeModal" v-show="showMealTypeModal" :date="dateString" @save="addMeal"/>
    </div>
</template>


<script type="text/javascript">
import { sortBy } from 'lodash';
import MealTypeModal from '../../modals/MealTypeModal';
import { getDateFromDateStringMeal, getMealUnitType, parseMealTypeToString, getDayEnumFromDateStringMeal } from '../../../utils/misc';
import { ERROR_MESSAGES, isPositiveNumber } from '../../../utils/validations';

export default {
    props: ['name', 'time', 'portion', 'dateString', 'id'],
    data: function () {
        return {
            isFetching: false,
            showMealTypeModal: false,
            mealDate: '',
            ingredients: [],
            meals: [],
            selectedMealIndex: 0,
            searchInput: null,
        };
    },
    methods: {
        removeIngredient(index) {
            try {
                this.meals[this.selectedMealIndex].ingredients.splice(index, 1);
            } catch (e) {
            }
        },
        getMealTypeOptions() {
            return getMealUnitType();
        },
        closeModal() {
            this.showMealTypeModal = false;
        },
        openMealTypeModal() {
            this.showMealTypeModal = true;
        },
        searchIngredients() {
            if (this.isFetching) return;
            this.isFetching = true;
            if (this.searchInput && this.searchInput.trim().length > 0) {
                axios.get(`api/meals-query/${this.searchInput}`).then(response => {
                    this.isFetching = false;
                    this.ingredients = response.data.data;
                }).catch(() => {
                    this.isFetching = false;
                });
            }
        },
        addIngredientToActiveMeal(ingredient) {
            try {
                if (!this.validateIngredientToAdd(ingredient)) return;
                this.meals[this.selectedMealIndex].ingredients.push({
                    code: ingredient.code,
                    name: ingredient.name,
                    quantity: 0,
                    unit: null,
                    errors: {
                        quantity: null,
                        unit: null,
                    },
                });
            } catch (e) {
            }
        },
        renderPortion(p) {
            if (p > 1) return `${p} porções`;
            return `${p} porção`;
        },
        renderMealName(name) {
            return parseMealTypeToString(name);
        },
        removeMealByIndex(index) {
            if (this.meals.length > index) {
                this.meals.splice(index, 1);
            }
        },
        selectMealByIndex(index) {
            this.selectedMealIndex = index;
        },
        addMeal(name, time, portion) {
            if (name && portion && time) {
                if (!this.validateMealToAdd(name, time)) return
                this.meals.push({
                    name,
                    portion,
                    time,
                    ingredients: [],
                });
                this.meals = sortBy(this.meals, 'time');
                this.meals.forEach((m, index) => {
                    if (m.name === name) this.selectMealByIndex(index);
                });
                if (this.showMealTypeModal) this.showMealTypeModal = false;
            }
        },
        validateMealToAdd(name, time) {
            let isValid = true;
            if (this.meals) {
                this.meals.forEach(m => {
                    if (m.name === name) isValid = false;
                    if (m.time === time) isValid = false;
                });
            }
            if (!isValid) {
                this.showError('A refeição tem de ter um nome e hora diferentes das registadas');
            }
            return isValid;
        },
        showError(message) {
            this.$toasted.show(message, {
                type: 'error',
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
        validateIngredientToAdd(ingredient) {
            let isValid = true;
            this.meals[this.selectedMealIndex].ingredients.forEach(i => {
                if (i.code === ingredient.code) {
                    isValid = false;
                }
            });
            return isValid;
        },
        saveMeal() {
            let hasErrors = false;
            this.meals.forEach(m => {
                m.ingredients.forEach(i => {
                    if (i.unit === null) {
                        hasErrors = true;
                        i.errors.unit = ERROR_MESSAGES.mandatoryField;
                    }

                    if (i.quantity === null) {
                        hasErrors = true;
                        i.errors.quantity = ERROR_MESSAGES.mandatoryField;
                    } else if (isPositiveNumber(i.quantity)) {
                        hasErrors = true;
                        i.errors.quantity = ERROR_MESSAGES.positiveNumber;
                    }
                });
            });

            if (hasErrors) {
                this.showError(
                    "Por favor verifique os erros nos campos dos ingredientes em cada refeição",
                );
                return;
            }

            axios.post('api/meal-plans', {
                'userId': this.id,
                'date': getDateFromDateStringMeal(this.dateString),
                'dayOfWeek': getDayEnumFromDateStringMeal(this.dateString),
                'meals': this.meals,
            }).then(() => {
                this.isFetching = false;
                this.$emit('open-plan-list');
            }).catch(error => {
                this.isFetching = false;
                this.isFetching = false;
                const { response } = error;
                let message = ERROR_MESSAGES.unknownError;
                if (response) {
                    const { data } = response;
                    if (data && data.error) {
                        message = data.error;
                    }
                }
                this.showError(message);
            });
        }
    },
    mounted() {
        this.addMeal(this.name, this.time, this.portion);
    },
    components: {
        MealTypeModal,
    }
};
</script>
<style>
@media only screen and (max-width: 600px) {
    .break-column {
        flex-direction: column;
    }

    .w-all {
        width: 100% !important;
        margin: 0.5rem !important;
    }
}
</style>

