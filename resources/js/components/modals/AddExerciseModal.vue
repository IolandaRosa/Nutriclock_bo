<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Adicionar Exercício Desportivo / Tarefa Doméstica</span>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add-exercises-modal-input-name" class="green-label">Nome</label>
                            <div>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.name !== null }"
                                    id="add-exercises-modal-input-name"
                                    v-model.trim="name"
                                >
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add-exercises-modal-input-met" class="green-label">MET</label>
                            <div>
                                <input
                                    type="number"
                                    min="0"
                                    steps="0.1"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.met !== null }"
                                    id="add-exercises-modal-input-met"
                                    v-model.trim="met"
                                >
                                <div v-if="errors.met" class="invalid-feedback">
                                    {{ errors.met }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-bold btn btn-primary" @click="onSaveClick" id="add-exercises-modal-save-btn">
                            Guardar
                        </button>
                        <button class="btn-bold btn btn-secondary" @click="onCloseClick"
                                id="add-exercises-modal-close-btn">
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
import {ERROR_MESSAGES, isDecimalOrEmpty, isEmptyField, isStringBiggerThanMax,} from '../../utils/validations';

export default {
    props: ['selectedRow', 'isSport'],
    data() {
        return {
            name: '',
            met: '',
            id: null,
            errors: {
                name: null,
                met: null,
            },
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
            this.id = null;
            this.met = '';
        },
        resetErrors() {
            this.errors.name = null;
            this.errors.met = null;
        },
        onSaveClick() {
            this.resetErrors();
            if (isEmptyField(this.name)) {
                this.errors.name = ERROR_MESSAGES.mandatoryField;
                return;
            }

            if (isDecimalOrEmpty(this.met)) {
                this.errors.met = ERROR_MESSAGES.invalidValue;
                return;
            }

            if (isStringBiggerThanMax(this.name, 255)) {
                this.errors.name = `${ERROR_MESSAGES.maxCharacters} 255 caratéres`;
                return;
            }
            let bodyData = {
                name: this.name,
                met: this.met,
            };

            if (this.isSport) {
                if (this.id != null) this.updateSport(bodyData);
                else this.createSport(bodyData);
            } else {
                if (this.id != null) this.updateHousehold(bodyData);
                else this.createHousehold(bodyData);
            }
        },

        updateSport(bodyData) {
            axios.put(`api/exercises-static/${this.id}`, bodyData).then(() => {
                this.handleSuccess();
            }).catch(error => {
                this.handleError(error);
            });
        },
        createSport(bodyData) {
            axios.post('api/exercises-static', bodyData).then(() => {
                this.handleSuccess();
            }).catch(error => {
                this.handleError(error);
            });
        },
        updateHousehold(bodyData) {
            axios.put(`api/households/${this.id}`, bodyData).then(() => {
                this.handleSuccess();
            }).catch(error => {
                this.handleError(error);
            });
        },
        createHousehold(bodyData) {
            axios.post('api/households', bodyData).then(() => {
                this.handleSuccess();
            }).catch(error => {
                this.handleError(error);
            });
        },
        handleSuccess() {
            this.resetErrors();
            this.resetFields();
            this.$emit('save');
        },
        handleError(error) {
            const {response} = error;
            let message = ERROR_MESSAGES.unknownError;
            if (response) {
                const {data} = response;
                if (data && data.error) {
                    message = data.error;
                }
            }
            this.$toasted.show(message, {
                type: 'error',
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        }
    },
    watch: {
        selectedRow: function (newVal, oldVal) {
            console.log(newVal)
            if (newVal) {
                this.id = newVal.id;
                this.name = newVal.name;
                this.met = newVal.met;
            } else {
                this.id = null;
                this.name = '';
                this.met = '';
            }
        },
    },
};
</script>
