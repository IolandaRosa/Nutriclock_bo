<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" style="max-width: 500px">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Adicionar Ingredientes</span>
                    </div>
                    <div class="modal-body text-secondary m-0">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Pesquisar..."
                                           aria-describedby="basic-addon2" v-model="searchInput"
                                           v-on:change="searchIngredients">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                                @click="searchIngredients">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor"
                                                 class="bi bi-search" viewBox="0 0 16 16">
                                                <path
                                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="font-weight-bold mb-2">
                            Ingredientes disponíveis
                        </div>

                        <div class="overflow-auto p-3 p-sm-0" style="max-height: 200px">
                            <table class="table table-sm table-striped bg-white table-hover">
                                <tbody>
                                <tr :key="index" v-for="(ing, index) in results">
                                    <td>{{ ing.name }}</td>
                                    <td v-on:click="() => { addIngredient(ing) }"
                                        class="btn text-primary btn-link text-right w-100">Adicionar
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="overflow-auto mt-4 p-3 p-sm-0" style="max-height: 200px">
                            <div :key="index" v-for="(i, index) in ingredients"
                                 class="rounded bg-white with-shadow p-2 mb-2" style="max-width: 400px">
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
                                            min="0"
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
                                            <option :key="index" v-for="(i, index) in getMealTypeOptions()"
                                                    :value="i.value">
                                                {{ i.label }}
                                            </option>
                                        </select>

                                        <div v-if="i.errors.unit" class="invalid-feedback">
                                            {{ i.errors.unit }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Descrição:</label>
                                    <div class="col-sm-8">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model.trim="i.description"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pt-0">
                        <button class="btn-bold btn btn-primary" @click="onSaveClick">
                            Guardar
                        </button>
                        <button class="btn-bold btn btn-secondary" @click="onCloseClick">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {getMealUnitType} from "../../utils/misc";
import {ERROR_MESSAGES, isPositiveNumber} from "../../utils/validations";

export default {
    props: ['mealTypeId'],
    data() {
        return {
            searchInput: null,
            isFetching: false,
            results: [],
            ingredients: [],
        };
    },
    methods: {
        onCloseClick() {
            this.resetFields();
            this.$emit('close');
        },
        removeIngredient(index) {
            try {
                this.ingredients.splice(index, 1);
            } catch (e) {
            }
        },
        getMealTypeOptions() {
            return getMealUnitType();
        },
        addIngredient(ingredient) {
            try {
                if (!this.validateIngredientToAdd(ingredient)) return;
                this.ingredients.push({
                    code: ingredient.code,
                    name: ingredient.name,
                    quantity: 0,
                    unit: null,
                    description: null,
                    errors: {
                        quantity: null,
                        unit: null,
                    },
                });
            } catch (e) {
            }
        },
        searchIngredients() {
            if (this.searchInput && this.searchInput.trim().length > 0) {
                axios.get(`api/meals-query/${this.searchInput}`).then(response => {
                    this.results = response.data.data;
                }).catch(() => {
                });
            }
        },
        validateIngredientToAdd(ingredient) {
            let isValid = true;
            this.ingredients.forEach(i => {
                if (i.code === ingredient.code) {
                    isValid = false;
                }
            });
            return isValid;
        },
        resetFields() {
            this.searchInput = null;
            this.results = [];
            this.ingredients = [];
        },
        onSaveClick() {
            let hasErrors = false;
            this.ingredients.forEach(i => {
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

            if (hasErrors) {
                this.showError(
                    "Por favor verifique os erros nos campos dos ingredientes em cada refeição",
                );
                return;
            }

            if (this.isFetching) return;
            this.isFetching = true;

            axios.post(`api/ingredient/${this.mealTypeId}`, { 'ingredients': this.ingredients }).then(response => {
                this.resetFields();
                this.isFetching = false;
                this.$emit('save', response.data.data);
            }).catch(error => {
                this.isFetching = false;
                const { response } = error;
                let message = ERROR_MESSAGES.unknownError;
                if (response) {
                    const { data } = response;
                    if (data && data.error) {
                        message = data.error;
                    }
                    this.showError(message);
                }
            });
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
    },
};
</script>

<style>
.no-border {
    border-color: #00000000 !important;
}
</style>
