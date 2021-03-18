<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" style="max-width: 400px">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Dados da Refeição</span>
                    </div>
                    <div class="modal-body text-secondary m-0">
                        <div class="font-weight-bold mb-3 text-uppercase">
                            {{ date }}
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nome:</label>
                            <div class="col-sm-10">
                                <select
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.name !== null }"
                                    v-model="name">
                                    <option v-for="n in mealType" :value="n.value">{{ n.label }}</option>
                                </select>
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Hora:</label>
                            <div class="col-sm-10">
                                <input class="form-control"
                                       v-bind:class="{ 'is-invalid': errors.time !== null }"
                                       type="time" v-model="time">
                                <div v-if="errors.time" class="invalid-feedback">
                                    {{ errors.time }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Porções:</label>
                            <div class="col-sm-10">
                                <input
                                    type="number"
                                    min="0"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.portion !== null }"
                                    v-model.trim="portion"
                                >
                                <div v-if="errors.portion" class="invalid-feedback">
                                    {{ errors.portion }}
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
import { ERROR_MESSAGES, isEmptyField, isPositiveNumber } from '../../utils/validations';

export default {
    props: ['date'],
    data() {
        return {
            name: '',
            time: '',
            portion: 0,
            errors: {
                name: null,
                time: null,
                portion: null,
            },
            mealType: [{
                value: 'P',
                label: 'Pequeno-almoço',
            }, {
                value: 'M',
                label: 'Meio da manhã',
            }, {
                value: 'A',
                label: 'Almoço',
            }, {
                value: 'J',
                label: 'Jantar',
            }, {
                value: 'L',
                label: 'Lanche',
            }, {
                value: 'O',
                label: 'Ceia',
            }, {
                value: 'S',
                label: 'Snack',
            }],
        };
    },
    methods: {
        onCloseClick() {
            this.resetErrors();
            this.resetFields();
            this.$emit('close');
        },
        resetFields() {
            this.name = '';
            this.time = this.getCurrentTime();
            this.portion = 0;
        },
        resetErrors() {
            this.errors.name = null;
            this.errors.time = null;
            this.errors.portion = null;
        },
        onSaveClick() {
            this.resetErrors();
            let hasErrors = false;
            if (isEmptyField(this.name)) {
                this.errors.name = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }

            if (isEmptyField(this.time)) {
                this.errors.time = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }

            if (isPositiveNumber(this.portion)) {
                this.errors.portion = ERROR_MESSAGES.positiveNumber;
                hasErrors = true;
            }

            if (hasErrors) return;
            this.$emit('save', this.name, this.time, this.portion, this.date);
            this.resetFields();
        },
        getCurrentTime() {
            const d = new Date();
            return `${d.getHours() > 9 ? d.getHours(): '0'+d.getHours()}:${d.getMinutes() > 9 ? d.getMinutes(): '0'+d.getMinutes()}`;
        }
    },
    mounted() {
        this.time = this.getCurrentTime();
    }
};
</script>

<style>
.no-border {
    border-color: #00000000 !important;
}
</style>
