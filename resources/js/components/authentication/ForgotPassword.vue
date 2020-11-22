<template>
    <div class="wrapper-center">
        <div id="forgot-password-form-container" class="white-container">
            <div class="mb-3">
                <img :src="'images/logo_text_horizontal.png'" alt="" />
            </div>
            <form class="needs-validation login-form" novalidate >
                <div class="col-md-8 mb-3">
                    <input type="text"
                           class="form-control"
                           v-bind:class="{ 'is-invalid': errors.email !== null }"
                           id="forgot-password-form-input-email"
                           placeholder="email"
                           v-model.trim="email"
                    >
                    <div v-if="errors.email" class="invalid-feedback">
                        {{errors.email}}
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <button class="full-width btn btn-primary" v-on:click.prevent="submit" type="submit">
                        <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Enviar Email</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import {
        ERROR_MESSAGES,
        isEmailFormatInvalid,
        isEmptyField
    } from '../../utils/validations';
    import { ROUTE } from '../../utils/routes';

    export default {
        data: function(){
            return {
                email: "",
                errors: {
                    email: null,
                },
                isFetching: false,
            };
        },
        methods: {
            submit() {
                this.errors.email = null;

                if (isEmptyField(this.email)) {
                    this.errors.email=ERROR_MESSAGES.mandatoryField;
                    return;
                }

                if (isEmailFormatInvalid(this.email)) {
                    this.errors.email=ERROR_MESSAGES.invalidFormat;
                    return;
                }

                if (this.isFetching) return;
                this.isFetching = true;

                axios.post('api/password', {
                    email: this.email,
                }).then(() => {
                    this.isFetching = false;
                    this.$toasted.show('Foi enviado um email para a recuperação de password!', {
                        type: 'success',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                    this.$router.push({path: ROUTE.Login});
                }).catch((error) => {
                    this.isFetching = false;
                    const { response } = error;
                    if (response) {
                        const { data } = response;
                        this.errors.email = data.error;
                    } else {
                        this.errors.email = ERROR_MESSAGES.unknownError;
                    }
                });
            }
        },
    };
</script>
