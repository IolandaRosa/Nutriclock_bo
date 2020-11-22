<template>
    <div class="wrapper-center">
        <div id="reset-passsword-form-container" class="white-container">
            <div class="mb-3">
                <img :src="'images/logo_text_horizontal.png'" alt=""/>
            </div>
            <form class="needs-validation login-form" novalidate>
                <div class="col-md-8 mb-3">
                    <input
                        type="password"
                        class="form-control"
                        v-bind:class="{ 'is-invalid': errors.password !== null }"
                        id="reset-password-form-input-password"
                        v-model.trim="password"
                        placeholder="Nova password"
                    >
                    <div v-if="errors.password" class="invalid-feedback">
                        {{errors.password}}
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <input
                        type="password"
                        class="form-control"
                        v-bind:class="{ 'is-invalid': errors.confirmationPassword !== null }"
                        id="reset-password-form-input-confirmation-password"
                        v-model.trim="confirmationPassword"
                        placeholder="Confirmação de Password"
                    >
                    <div v-if="errors.confirmationPassword" class="invalid-feedback">
                        {{errors.confirmationPassword}}
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <button class="full-width btn btn-primary" v-on:click.prevent="submit" type="submit">
                        <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                              aria-hidden="true"></span>
                        <span>Guardar Password</span>
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
        equalFields,
        isEmptyField,
    } from '../../utils/validations';
    import { ROUTE } from '../../utils/routes';
    import {UserRoles} from "../../constants/misc";

    export default {
        data: function () {
            return {
                password: "",
                confirmationPassword: "",
                errors: {
                    password: null,
                    confirmationPassword: null,
                },
                isFetching: false,
            };
        },
        methods: {
            showMessage(message) {
                this.$toasted.show(message, {
                    type: 'success',
                    duration: 3000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
            },
            submit() {
                let hasErrors = false;
                this.errors.password = null;
                this.errors.confirmationPassword = null;

                if (isEmptyField(this.password)) {
                    this.errors.password = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmptyField(this.confirmationPassword)) {
                    this.errors.confirmationPassword = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (!equalFields(this.password, this.confirmationPassword)) {
                    this.errors.password = ERROR_MESSAGES.matchField;
                    this.errors.confirmationPassword = ERROR_MESSAGES.matchField;
                    hasErrors = true;
                }

                if (hasErrors) return;
                if (this.isFetching) return;
                this.isFetching = true;
                if (!this.$route.params.token) return;

                axios.post('api/reset-password', {
                    password: this.password,
                    token: this.$route.params.token,
                }).then((response) => {
                    const { data } = response.data;

                    if (data.role === UserRoles.Patient) {
                        this.showMessage('Password recuperada com sucesso. Volte à aplicação para fazer login!');
                    } else {
                        this.$store.commit('setUser', data);
                        this.showMessage('Password recuperada com sucesso!');
                        return axios.post('api/login', {
                            email: data.email,
                            password: this.password,
                        });
                    }
                }).then(response => {
                    this.isFetching = false;
                    if (response) {
                        this.$store.commit('setToken', response.data.access_token);
                        this.$router.push({path: ROUTE.Profile});
                    }
                }).catch((error) => {
                    this.isFetching = false;
                    const {response} = error;
                    if (response) {
                        const {data} = response;
                        this.errors.password = data.error;
                    } else {
                        this.errors.password = ERROR_MESSAGES.unknownError;
                    }
                });
            }
        },
    };
</script>
