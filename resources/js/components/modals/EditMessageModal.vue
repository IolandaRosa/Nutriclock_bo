<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-body text-dark" style="margin: 0">
                        <div class="card bg-dark text-light mt-2">
                            <div class="card-header px-3 d-flex align-items-center py-2">
                                <div class="font-weight-bolder flex-grow-1">Edite a mensagem</div>
                            </div>
                            <div class="card-body py-2">
                                <textarea v-bind:class="[errors.message !== null ? 'form-control is-invalid' : 'form-control' ]" rows="7" v-model="message"></textarea>
                                <div v-if="errors.message" class="invalid-feedback">
                                    {{errors.message}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 0 0.75rem 0 0">
                        <button class="btn-bold btn btn-dark" @click="onSaveClick">
                            Ok
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

import { ERROR_MESSAGES, isEmptyField } from '../../utils/validations';
import { makeSocketEvent } from '../../utils/misc';
import { EventType } from '../../constants/misc';

export default {
    props: ['selectedMessage'],
    data() {
        return {
            message: null,
            errors: {
                message: null,
            }
        };
    },
    methods: {
        onCloseClick() {
            this.resetErrors();
            this.resetFields();
            this.$emit('close');
        },
        resetFields(){
            this.message = null;
        },
        resetErrors() {
            this.errors.message = null;
        },
        onSaveClick() {
            this.resetErrors();
            if (isEmptyField(this.message)) {
                this.errors.message = ERROR_MESSAGES.mandatoryField;
                return
            }
            this.$emit('save', this.message);
            this.resetFields();
        },
    },
    watch: {
        selectedMessage: function (newVal, oldVal) {
            if (newVal) this.message = newVal.message;
        },
    },
};
</script>
