<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">{{title}}</span>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add-disease-modal-input-name" class="green-label">Nome</label>
                            <div>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.name !== null }"
                                    id="add-disease-modal-input-name"
                                    v-model.trim="name"
                                    placeholder="Ex: Doença Cardíaca"
                                >
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{errors.name}}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add-disease-modal-select-type" class="green-label">Tipo</label>
                            <div>
                                <select
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.type !== null }"
                                    id="add-disease-modal-select-type"
                                    v-model="type">
                                    <option v-for="type in types" :value="type.value">{{type.label}}</option>
                                </select>
                            </div>
                            <div v-if="errors.type" class="invalid-feedback">
                                {{errors.type}}
                            </div>
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
    import {ERROR_MESSAGES, isEmptyField, isStringBiggerThanMax} from '../../utils/validations';

    export default {
        props: ['title', 'selectedDiseaseId', 'selectedDiseaseName', 'selectedDiseaseType'],
        data() {
            return {
                name: '',
                type: '',
                types: [
                    {
                        value: 'A',
                        label: 'Alergia Alimentar',
                    },
                    {
                        value: 'D',
                        label: 'Patologia',
                    }
                ],
                id: null,
                errors: {
                    name: null,
                    type: null,
                },
            };
        },
        methods: {
            onCloseClick() {
                this.$emit('close');
            },
            onSaveClick() {
                this.errors.name = null;
                this.errors.type = null;
                if (isEmptyField(this.name)) {
                    this.errors.name = ERROR_MESSAGES.mandatoryField;
                    return;
                }

                if(isEmptyField(this.type)) {
                    this.errors.type = ERROR_MESSAGES.mandatoryField;
                    return;
                }

                if (isStringBiggerThanMax(this.name, 255)) {
                    this.errors.name = `${ERROR_MESSAGES.maxCharacters} 255 caratéres`;
                    return;
                }
                this.$emit('save', this.name, this.type,  this.id);
            },
        },
        watch: {
            selectedDiseaseId: function (newVal, oldVal) {
                this.id = newVal;
            },
            selectedDiseaseName: function (newVal, oldVal) {
                this.name = newVal;
            },
            selectedDiseaseType: function (newVal, oldVal) {
                this.type = newVal;
            },
        },
    };
</script>
