<template>
    <div class="wrapper-center">
        <div id="login-form-container" class="white-container">
            <div class="mb-3">
                <img src="http://nutriclock.test:81/images/logo_text_horizontal.png" alt=""/>
            </div>
            <form class="needs-validation login-form" novalidate>
                <div class="col-md-8 mb-3">
                    <input type="text"
                           class="form-control"
                           v-bind:class="{ 'is-invalid': errors.email !== null }"
                           id="login-form-input-email"
                           placeholder="email"
                           v-model.trim="email"
                    >
                    <div v-if="errors.email" class="invalid-feedback">
                        {{errors.email}}
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <input
                        type="password"
                        class="form-control"
                        v-bind:class="{ 'is-invalid': errors.password !== null }"
                        id="login-form-input-password"
                        v-model.trim="password"
                        placeholder="password"
                    >
                    <div v-if="errors.password" class="invalid-feedback">
                        {{errors.password}}
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <button class="full-width btn btn-primary" v-on:click.prevent="submit" type="submit">
                        <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                              aria-hidden="true"></span>
                        <span>Login</span>
                    </button>
                </div>
            </form>
            <div>
                <a href="#" v-on:click.prevent="redirectForgotPassword" class="password-link">Recuperar a Password</a>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import {
        ERROR_MESSAGES,
        isEmailFormatInvalid,
        isEmptyField,
    } from '../../utils/validations';
    import {ROUTE} from "../../utils/routes";
    import {UserRoles} from "../../constants/misc";

    export default {
        data: function () {
            return {
                email: "",
                password: "",
                errors: {
                    email: null,
                    password: null,
                },
                isFetching: false,
            };
        },
        methods: {
            submit() {
                let hasErrors = false;
                this.errors.email = null;
                this.errors.password = null;

                if (isEmptyField(this.email)) {
                    this.errors.email = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmptyField(this.password)) {
                    this.errors.password = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmailFormatInvalid(this.email)) {
                    this.errors.email = ERROR_MESSAGES.invalidFormat;
                    hasErrors = true;
                }

                if (hasErrors) return;

                if (this.isFetching) return;

                this.isFetching = true;

                axios.post('api/login', {
                    email: this.email,
                    password: this.password,
                }).then(response => {
                    this.$store.commit('setToken', response.data.access_token);
                    return axios.get('api/users/me');
                }).then(response => {
                    if (response.data.data.role === UserRoles.Patient) throw new Error();
                    this.$store.commit('setUser', response.data.data);
                    this.$router.push({path: ROUTE.AdminUsers});
                    this.isFetching = false;
                }).catch(error => {
                    this.$store.commit('clearUserAndToken');
                    this.errors.email = ERROR_MESSAGES.invalidCredentials;
                    this.errors.password = ERROR_MESSAGES.invalidCredentials;
                    this.isFetching = false;
                });
            },
            redirectForgotPassword(){
                if (this.isFetching) return;
                this.$router.push({path: ROUTE.ForgotPassword});
            }
        },
    };
</script>
<style>
    .password-link {
        color: #74D44D;
        display: flex;
        justify-content: center;
    }

    .password-link:hover {
        color: #74D44D;
        text-decoration: underline;
        background: transparent;
    }
</style>

