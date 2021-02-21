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
                            <label for="add-category-modal-input-name" class="green-label">Nome</label>
                            <div>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.name !== null }"
                                    id="add-category-modal-input-name"
                                    v-model.trim="name"
                                    :placeholder="placeholderName"
                                >
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{errors.name}}
                                </div>
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
    import { ERROR_MESSAGES, isEmptyField, isStringBiggerThanMax } from '../../utils/validations';

    export default{
        props:['title', 'selectedName', 'selectedId', 'placeholderName', 'maxChar'],
        data() {
            return {
                name: '',
                id: null,
                errors: {
                    name: null,
                },
            };
        },
        methods:{
            onCloseClick() {
                this.resetErrors();
                this.resetFields();
                this.$emit('close');
            },
            resetFields() {
                this.name = '';
                this.id = null;
            },
            resetErrors() {
                this.errors.name = null;
            },
            onSaveClick() {
                this.resetErrors();
                if (isEmptyField(this.name)) {
                    this.errors.name = ERROR_MESSAGES.mandatoryField;
                    return;
                }

                if(isStringBiggerThanMax(this.name, this.maxChar)) {
                    this.errors.name = `${ERROR_MESSAGES.maxCharacters} ${this.maxChar} caratéres`;
                    return;
                }

                this.$emit('save', this.name, this.id);
                this.resetFields();
            },
        },
        watch: {
            selectedName: function(newVal, oldVal) {
                this.name = newVal;
            },
            selectedId: function(newVal, oldVal) {
                this.id = newVal;
            }
        },
    };
</script>
