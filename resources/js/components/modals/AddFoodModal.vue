<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container text-info">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">{{ title }}</span>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col">
                                <label for="inputCode">Código</label>
                                <input
                                   :readonly="isEdit"
                                    type="text"
                                    class="form-control"
                                    id="inputCode"
                                    v-bind:class="{ 'is-invalid': errors.code !== null }"
                                    v-model.trim="code"
                                >
                                <div v-if="errors.code" class="invalid-feedback">
                                    {{ errors.code }}
                                </div>
                            </div>
                            <div class="col-7">
                                <label for="inputName">Nome</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="inputName"
                                    v-bind:class="{ 'is-invalid': errors.name !== null }"
                                    v-model.trim="name"
                                >
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="text-dark mt-3 mb-1 font-weight-bold">Macronutrientes</div>
                        <div class="form-row">
                            <div class="col">
                                <label for="inputEnergyKcal">Energia (kcal)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputEnergyKcal"
                                    v-bind:class="{ 'is-invalid': errors.energy_kcal !== null }"
                                    v-model.trim="energy_kcal"
                                >
                                <div v-if="errors.energy_kcal" class="invalid-feedback">
                                    {{ errors.energy_kcal }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputEnergyKJ">Energia (kJ)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputEnergyKJ"
                                    v-bind:class="{ 'is-invalid': errors.energy_kJ !== null }"
                                    v-model.trim="energy_kJ"
                                >
                                <div v-if="errors.energy_kJ" class="invalid-feedback">
                                    {{ errors.energy_kJ }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputWater">Água (gr)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputWater"
                                    v-bind:class="{ 'is-invalid': errors.water_g !== null }"
                                    v-model.trim="water_g"
                                >
                                <div v-if="errors.water_g" class="invalid-feedback">
                                    {{ errors.water_g }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputProtein">Proteína (gr)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputProtein"
                                    v-bind:class="{ 'is-invalid': errors.protein_g !== null }"
                                    v-model.trim="protein_g"
                                >
                                <div v-if="errors.protein_g" class="invalid-feedback">
                                    {{ errors.protein_g }}
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="inputFat">Gorduras (gr)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputFat"
                                    v-bind:class="{ 'is-invalid': errors.fats_g !== null }"
                                    v-model.trim="fats_g"
                                >
                                <div v-if="errors.fats_g" class="invalid-feedback">
                                    {{ errors.fats_g }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputCarboHidrats">Hid. Carbono (gr)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputCarboHidrats"
                                    v-bind:class="{ 'is-invalid': errors.carbo_hidrats_g !== null }"
                                    v-model.trim="carbo_hidrats_g"
                                >
                                <div v-if="errors.carbo_hidrats_g" class="invalid-feedback">
                                    {{ errors.carbo_hidrats_g }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputFiber">Fibra (gr)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputFiber"
                                    v-bind:class="{ 'is-invalid': errors.fiber_g !== null }"
                                    v-model.trim="fiber_g"
                                >
                                <div v-if="errors.fiber_g" class="invalid-feedback">
                                    {{ errors.fiber_g }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputColesterol">Colestrol (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputColesterol"
                                    v-bind:class="{ 'is-invalid': errors.colesterol_mg !== null }"
                                    v-model.trim="colesterol_mg"
                                >
                                <div v-if="errors.colesterol_mg" class="invalid-feedback">
                                    {{ errors.colesterol_mg }}
                                </div>
                            </div>
                        </div>

                        <div class="text-dark mt-3 mb-1 font-weight-bold">Vitaminas</div>
                        <div class="form-row">
                            <div class="col">
                                <label for="inputVitA">Vit A (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputVitA"
                                    v-bind:class="{ 'is-invalid': errors.vitA_mg !== null }"
                                    v-model.trim="vitA_mg"
                                >
                                <div v-if="errors.vitA_mg" class="invalid-feedback">
                                    {{ errors.vitA_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputVitD">Vit D (pg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputVitD"
                                    v-bind:class="{ 'is-invalid': errors.vitD_pg !== null }"
                                    v-model.trim="vitD_pg"
                                >
                                <div v-if="errors.vitD_pg" class="invalid-feedback">
                                    {{ errors.vitD_pg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputTiamina">Tiamina (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputTiamina"
                                    v-bind:class="{ 'is-invalid': errors.tiamina_mg !== null }"
                                    v-model.trim="tiamina_mg"
                                >
                                <div v-if="errors.tiamina_mg" class="invalid-feedback">
                                    {{ errors.tiamina_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputRiboflavina">Riboflavina (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputRiboflavina"
                                    v-bind:class="{ 'is-invalid': errors.riboflavina_mg !== null }"
                                    v-model.trim="riboflavina_mg"
                                >
                                <div v-if="errors.riboflavina_mg" class="invalid-feedback">
                                    {{ errors.riboflavina_mg }}
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="inputNiacina">Niacina (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputNiacina"
                                    v-bind:class="{ 'is-invalid': errors.niacina_mg !== null }"
                                    v-model.trim="niacina_mg"
                                >
                                <div v-if="errors.niacina_mg" class="invalid-feedback">
                                    {{ errors.niacina_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputVitB6">Vit B6 (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputVitB6"
                                    v-bind:class="{ 'is-invalid': errors.vitB6_mg !== null }"
                                    v-model.trim="vitB6_mg"
                                >
                                <div v-if="errors.vitB6_mg" class="invalid-feedback">
                                    {{ errors.vitB6_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputVitB12">Vit B12 (pg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputVitB12"
                                    v-bind:class="{ 'is-invalid': errors.vit_B12_pg !== null }"
                                    v-model.trim="vit_B12_pg"
                                >
                                <div v-if="errors.vit_B12_pg" class="invalid-feedback">
                                    {{ errors.vit_B12_pg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputVitC">Vit C (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputVitC"
                                    v-bind:class="{ 'is-invalid': errors.vitC_mg !== null }"
                                    v-model.trim="vitC_mg"
                                >
                                <div v-if="errors.vitC_mg" class="invalid-feedback">
                                    {{ errors.vitC_mg }}
                                </div>
                            </div>
                        </div>

                        <div class="text-dark mt-3 mb-1 font-weight-bold">Minerais</div>
                        <div class="form-row">
                            <div class="col">
                                <label for="inputNa">Sódio (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputNa"
                                    v-bind:class="{ 'is-invalid': errors.na_mg !== null }"
                                    v-model.trim="na_mg"
                                >
                                <div v-if="errors.na_mg" class="invalid-feedback">
                                    {{ errors.na_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputK">Potássio (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputK"
                                    v-bind:class="{ 'is-invalid': errors.k_mg !== null }"
                                    v-model.trim="k_mg"
                                >
                                <div v-if="errors.k_mg" class="invalid-feedback">
                                    {{ errors.k_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputCa">Cálcio (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputCa"
                                    v-bind:class="{ 'is-invalid': errors.ca_mg !== null }"
                                    v-model.trim="ca_mg"
                                >
                                <div v-if="errors.ca_mg" class="invalid-feedback">
                                    {{ errors.ca_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputP">Fósforo (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputP"
                                    v-bind:class="{ 'is-invalid': errors.p_mg !== null }"
                                    v-model.trim="p_mg"
                                >
                                <div v-if="errors.p_mg" class="invalid-feedback">
                                    {{ errors.p_mg }}
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="inputMg">Magnésio (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputMg"
                                    v-bind:class="{ 'is-invalid': errors.mg_mg !== null }"
                                    v-model.trim="mg_mg"
                                >
                                <div v-if="errors.mg_mg" class="invalid-feedback">
                                    {{ errors.mg_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputFe">Ferro (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputFe"
                                    v-bind:class="{ 'is-invalid': errors.fe_mg !== null }"
                                    v-model.trim="fe_mg"
                                >
                                <div v-if="errors.fe_mg" class="invalid-feedback">
                                    {{ errors.fe_mg }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="inputZn">Zinco (mg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    id="inputZn"
                                    v-bind:class="{ 'is-invalid': errors.zn_mg !== null }"
                                    v-model.trim="zn_mg"a
                                >
                                <div v-if="errors.zn_mg" class="invalid-feedback">
                                    {{ errors.zn_mg }}
                                </div>
                            </div>
                            <div class="col"/>
                        </div>
                    </div>
                    <div class="modal-footer">
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
import { isEmptyField, ERROR_MESSAGES, isDecimalOrEmpty } from '../../utils/validations';

export default {
    props: ['title', 'selectedItem'],
    data() {
        return {
            isEdit: false,
            name: '',
            code: '',
            energy_kcal: '',
            energy_kJ: '',
            water_g: '',
            protein_g: '',
            fats_g: '',
            carbo_hidrats_g: '',
            fiber_g: '',
            colesterol_mg: '',
            vitA_mg: '',
            vitD_pg: '',
            tiamina_mg: '',
            riboflavina_mg: '',
            niacina_mg: '',
            vitB6_mg: '',
            vit_B12_pg: '',
            vitC_mg: '',
            na_mg: '',
            k_mg: '',
            ca_mg: '',
            p_mg: '',
            mg_mg: '',
            fe_mg: '',
            zn_mg: '',
            errors: {
                code: null,
                name: null,
                energy_kcal: null,
                energy_kJ: null,
                water_g: null,
                protein_g: null,
                fats_g: null,
                carbo_hidrats_g: null,
                fiber_g: null,
                colesterol_mg: null,
                vitA_mg: null,
                vitD_pg: null,
                tiamina_mg: null,
                riboflavina_mg: null,
                niacina_mg: null,
                vitB6_mg: null,
                vit_B12_pg: null,
                vitC_mg: null,
                na_mg: null,
                k_mg: null,
                ca_mg: null,
                p_mg: null,
                mg_mg: null,
                fe_mg: null,
                zn_mg: null,
            }
        };
    },
    methods: {
        onCloseClick() {
            this.resetErrors();
            this.resetFields();
            this.$emit('close');
        },
        resetFields() {
            this.isEdit = false;
            this.name = '';
            this.code = '';
            this.energy_kcal = '';
            this.energy_kJ = '';
            this.water_g = '';
            this.protein_g = '';
            this.fats_g = '';
            this.carbo_hidrats_g = '';
            this.fiber_g = '';
            this.colesterol_mg = '';
            this.vitA_mg = '';
            this.vitD_pg = '';
            this.tiamina_mg = '';
            this.riboflavina_mg = '';
            this.niacina_mg = '';
            this.vitB6_mg = '';
            this.vit_B12_pg = '';
            this.vitC_mg = '';
            this.na_mg = '';
            this.k_mg = '';
            this.ca_mg = '';
            this.p_mg = '';
            this.mg_mg = '';
            this.fe_mg = '';
            this.zn_mg = '';
        },
        resetErrors() {
            this.errors.code = null;
            this.errors.name = null;
            this.errors.energy_kcal = null;
            this.errors.energy_kJ = null;
            this.errors.water_g = null;
            this.errors.protein_g = null;
            this.errors.fats_g = null;
            this.errors.fiber_g = null;
            this.errors.colesterol_mg = null;
            this.errors.vitD_pg = null;
            this.errors.vitA_mg = null;
            this.errors.tiamina_mg = null;
            this.errors.riboflavina_mg = null;
            this.errors.niacina_mg = null;
            this.errors.vitB6_mg = null;
            this.errors.vit_B12_pg = null;
            this.errors.vitC_mg = null;
            this.errors.na_mg = null;
            this.errors.k_mg = null;
            this.errors.ca_mg = null;
            this.errors.p_mg = null;
            this.errors.mg_mg = null;
            this.errors.fe_mg = null;
            this.errors.zn_mg = null;
        },
        onSaveClick() {
            this.resetErrors();
            if (this.validateFields()) return;
            let bodyData = {
                name: this.name,
                code: this.code,
                energy_kcal: this.energy_kcal,
                energy_kJ: this.energy_kJ,
                water_g: this.water_g,
                protein_g: this.protein_g,
                fats_g: this.fats_g,
                carbo_hidrats_g: this.carbo_hidrats_g,
                fiber_g: this.fiber_g,
                colesterol_mg: this.colesterol_mg,
                vitA_mg: this.vitA_mg,
                vitD_pg: this.vitD_pg,
                tiamina_mg: this.tiamina_mg,
                riboflavina_mg: this.riboflavina_mg,
                niacina_mg: this.niacina_mg,
                vitB6_mg: this.vitB6_mg,
                vit_B12_pg: this.vit_B12_pg,
                vitC_mg: this.vitC_mg,
                na_mg: this.na_mg,
                k_mg: this.k_mg,
                ca_mg: this.ca_mg,
                p_mg: this.p_mg,
                mg_mg: this.mg_mg,
                fe_mg: this.fe_mg,
                zn_mg: this.zn_mg,
            };

            if (this.isEdit) {
                axios.put(`api/nutritional-info-static/${this.code}`, bodyData).then(() => {
                    this.resetFields();
                    this.resetErrors();
                    this.$emit('save');
                }).catch(error => {
                    console.log(error)
                });
                return
            }

            axios.post('api/nutritional-info-static', bodyData).then(() => {
                this.resetFields();
                this.resetErrors();
                this.$emit('save');
            }).catch(() => {});

        },
        validateFields() {
            let hasErrors = false
            if (isEmptyField(this.code)) {
                this.errors.code = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isEmptyField(this.name)) {
                this.errors.name = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.energy_kcal)) {
                this.errors.energy_kcal = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.energy_kJ)) {
                this.errors.energy_kJ = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.water_g)) {
                this.errors.water_g = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.protein_g)) {
                this.errors.protein_g = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.fats_g)) {
                this.errors.fats_g = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.fiber_g)) {
                this.errors.fiber_g = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.colesterol_mg)) {
                this.errors.colesterol_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.vitD_pg)) {
                this.errors.vitD_pg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.vitA_mg)) {
                this.errors.vitA_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.tiamina_mg)) {
                this.errors.tiamina_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.fats_g)) {
                this.errors.fats_g = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.riboflavina_mg)) {
                this.errors.riboflavina_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.niacina_mg)) {
                this.errors.niacina_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.vitB6_mg)) {
                this.errors.vitB6_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.vitA_mg)) {
                this.errors.vitA_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.vit_B12_pg)) {
                this.errors.vit_B12_pg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.vitC_mg)) {
                this.errors.vitC_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.na_mg)) {
                this.errors.na_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.k_mg)) {
                this.errors.k_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.ca_mg)) {
                this.errors.ca_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.p_mg)) {
                this.errors.p_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.mg_mg)) {
                this.errors.mg_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.fe_mg)) {
                this.errors.fe_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            if (isDecimalOrEmpty(this.zn_mg)) {
                this.errors.zn_mg = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }
            return hasErrors
        }
    },
    watch: {
        selectedItem: function (newVal, oldVal) {
            this.isEdit = false

            if (newVal) {
                this.isEdit = true;
                this.code = newVal.code;
                this.name = newVal.name;
                this.energy_kcal = newVal.energy_kcal;
                this.energy_kJ = newVal.energy_kJ;
                this.water_g = newVal.water_g;
                this.protein_g = newVal.protein_g;
                this.fats_g = newVal.fats_g;
                this.carbo_hidrats_g = newVal.carbo_hidrats_g;
                this.fiber_g = newVal.fiber_g;
                this.colesterol_mg = newVal.colesterol_mg;
                this.vitA_mg = newVal.vitA_mg;
                this.vitD_pg = newVal.vitD_pg;
                this.tiamina_mg = newVal.tiamina_mg;
                this.riboflavina_mg = newVal.riboflavina_mg;
                this.niacina_mg = newVal.niacina_mg;
                this.vitB6_mg = newVal.vitB6_mg;
                this.vit_B12_pg = newVal.vit_B12_pg;
                this.vitC_mg = newVal.vitC_mg;
                this.na_mg = newVal.na_mg;
                this.k_mg = newVal.k_mg;
                this.ca_mg = newVal.ca_mg;
                this.p_mg = newVal.p_mg;
                this.mg_mg = newVal.mg_mg;
                this.fe_mg = newVal.fe_mg;
                this.zn_mg = newVal.zn_mg;
            }
        },
    }
};
</script>
