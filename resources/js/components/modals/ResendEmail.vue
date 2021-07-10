<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="`/images/only_logo.png`" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Reenviar Email</span>
                    </div>
                    <div class="modal-body">
                        <p style="color: #000; text-align: center">Reenviar o email de confirmação de registo.</p>
                        <div class="form-group">
                            <label for="resend-email-modal-input-name" class="green-label">Email</label>
                            <div>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.email !== null }"
                                    id="resend-email-modal-input-name"
                                    v-model.trim="email"
                                    placeholder="email@mail.pt"
                                >
                                <div v-if="errors.email" class="invalid-feedback">
                                    {{errors.email}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-bold btn btn-primary" @click="onSaveClick" id="resend-email-modal-save-btn">
                            Reenviar
                        </button>
                        <button class="btn-bold btn btn-secondary" @click="onCloseClick" id="resend-email-modal-close-btn">
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
    import { ERROR_MESSAGES, isEmailFormatInvalid, isEmptyField } from '../../utils/validations';

    export default{
        props:['selectedEmail'],
        data() {
            return {
                email: '',
                id: null,
                errors: {
                    email: null,
                },
            };
        },
        methods:{
            onCloseClick() {
                this.resetErrors();
                this.resetFields();
                this.$emit('close');
            },
            resetErrors() {
                this.errors.email = null;
            },
            resetFields() {
                this.email = '';
                this.id = null;
            },
            onSaveClick() {
                this.resetErrors();
                if (isEmptyField(this.email)) {
                    this.errors.email = ERROR_MESSAGES.mandatoryField;
                    return;
                }

                if (isEmailFormatInvalid(this.email)) {
                    this.errors.email = ERROR_MESSAGES.invalidFormat;
                    return;
                }

                this.$emit('save', this.email);
                this.resetFields();
            },
        },
        watch: {
            selectedEmail: function(newVal, oldVal) {
                this.email = newVal;
            },
        },
    };
</script>
