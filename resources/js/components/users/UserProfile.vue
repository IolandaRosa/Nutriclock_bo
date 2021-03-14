<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-px-4 pb-4 pt-1 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <div class="component-wrapper-left">
                        <FileUpload
                            :image-url="avatarUrl"
                            @onFileSelected="this.onFileSelected"
                            :disabled="false"
                        />
                    </div>
                    <div class="component-wrapper-right">
                        <button class="btn-bold btn btn-primary" v-on:click.prevent="save" type="button"
                                data-toggle="tooltip"
                                title="Guardar alterações">
                    <span v-if="isFetching" class="spinner-border spinner-border-sm" role="status"
                          aria-hidden="true"></span>
                            <span>Guardar alterações</span>
                        </button>
                    </div>
                </div>
                <div>
                    <div class="text-info font-weight-bold">{{ role }}</div>
                    <div v-show="this.$store.state.user.role === 'PROFESSIONAL'">
                        <span class="text-dark font-weight-bold mr-1">Categoria Profissional:</span>
                        <span class="text-secondary font-weight-normal">{{ userCategory }}</span>
                    </div>
                    <div v-show="this.$store.state.user.role === 'PROFESSIONAL'">
                        <span class="text-dark font-weight-bold mr-1">Instituição:</span>
                        <span class="text-secondary font-weight-normal">{{ userUfcs }}</span>
                    </div>
                </div>
                <div>
                    <div class="card-deck">
                        <div class="card p-2 pt-3 mt-2 text-dark">
                            <h5 class="card-title">Dados Pessoais</h5>
                            <div class="card-text">
                                <div class="form-group">
                                    <label for="name" class="small-text">Nome</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        v-bind:class="{ 'is-invalid': errors.name !== null }"
                                        v-model="name"
                                    >
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-text">
                                <div class="form-group">
                                    <label class="small-text">Género</label>
                                    <div>
                                        <select
                                            class="form-control mb-2"
                                            style="height: 40px"
                                            v-bind:class="{ 'is-invalid': errors.gender !== null }"
                                            v-model="gender">
                                            <option value="MALE">Masculino</option>
                                            <option value="FEMALE">Feminino</option>
                                            <option value="NONE">Não me identifico</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-text">
                                <div class="form-group">
                                    <label for="user-profile-input-email"
                                           class="small-text">Email</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.name !== null }"
                                        id="user-profile-input-email"
                                        placeholder="email@mail.pt"
                                        v-model="email">
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card p-2 pt-3 mt-2 text-dark">
                            <h5 class="card-title">Alteração de Password</h5>
                            <div class="card-text">
                                <div class="form-group">
                                    <label for="user-profile-password"
                                           class="small-text">Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="user-profile-password"
                                        v-bind:class="{ 'is-invalid': errors.password !== null }"
                                        placeholder="password"
                                        v-model="password"
                                    >
                                    <div v-if="errors.password" class="invalid-feedback">
                                        {{ errors.password }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-text">
                                <div class="form-group">
                                    <label for="user-profile-new-password" class="small-text">Nova
                                        Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="user-profile-new-password"
                                        v-bind:class="{ 'is-invalid': errors.newPassword !== null }"
                                        placeholder="nova password"
                                        v-model="newPassword"
                                    >
                                    <div v-if="errors.newPassword" class="invalid-feedback">
                                        {{ errors.newPassword }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-text">
                                <div class="form-group">
                                    <label for="user-profile-password" class="small-text">Confirmação de
                                        Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="user-profile-passwordConfirmation"
                                        v-bind:class="{ 'is-invalid': errors.confirmationPassword !== null }"
                                        placeholder="confirmação password"
                                        v-model="confirmationPassword"
                                    >
                                    <div v-if="errors.confirmationPassword" class="invalid-feedback">
                                        {{ errors.confirmationPassword }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
import FileUpload from '../utils/FileUpload';
import {getCategoryNameById, renderGender, renderRole} from "../../utils/misc";
import {equalFields, ERROR_MESSAGES, isEmailFormatInvalid, isEmptyField} from "../../utils/validations";
import {ROUTE} from "../../utils/routes";
import {UserRoles} from "../../constants/misc";

export default {
    data: function () {
        return {
            name: '',
            avatarUrl: '',
            avatarImg: '',
            email: '',
            password: '',
            confirmationPassword: '',
            role: '',
            userCategory: '',
            gender: '',
            newPassword: '',
            userUfcs: '',
            errors: {
                name: null,
                newPassword: null,
                password: null,
                confirmationPassword: null,
                email: null,
                gender: null,
            },
            isFetching: false,
        };
    },
    methods: {
        onFileSelected(file) {
            this.avatarImg = file;
            this.avatarUrl = URL.createObjectURL(this.avatarImg);
        },
        save() {
            this.errors.name = null;
            this.errors.email = null;
            this.errors.password = null;
            this.errors.confirmationPassword = null;
            this.errors.gender = null;
            this.errors.newPassword = null;
            let hasErrors = false;

            if (isEmptyField(this.name)) {
                this.errors.name = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }

            if (isEmptyField(this.email)) {
                this.errors.email = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }

            if (isEmailFormatInvalid(this.email)) {
                this.errors.email = ERROR_MESSAGES.invalidFormat;
                hasErrors = true;
            }

            if (isEmptyField(this.gender)) {
                this.errors.gender = ERROR_MESSAGES.mandatoryField;
                hasErrors = true;
            }

            if (this.password) {
                if (!equalFields(this.newPassword, this.confirmationPassword)) {
                    this.errors.newPassword = ERROR_MESSAGES.matchField;
                    this.errors.confirmationPassword = ERROR_MESSAGES.matchField;
                    hasErrors = true;
                }

                if (equalFields(this.password, this.newPassword)) {
                    this.errors.password = ERROR_MESSAGES.notMatchFields;
                    this.errors.newPassword = ERROR_MESSAGES.notMatchFields;
                    hasErrors = true;
                }
            }

            if (hasErrors) return;
            const formData = new FormData();

            if (this.avatarImg != null) {
                formData.append('avatar', this.avatarImg);
            }

            formData.append('name', this.name);
            formData.append('email', this.email);
            formData.append('gender', this.gender);
            formData.append('_method', 'put');

            if (this.isFetching) return;

            this.isFetching = true;

            axios.post(`api/users/profile/${this.$store.state.user.id}`, formData).then(response => {
                this.isFetching = false;
                this.$store.commit('setUser', response.data.data);
            }).catch(() => {
                this.isFetching = false;
            });


            if (this.password) {
                axios.put(`api/password/${this.$store.state.user.id}`, {
                    password: this.password,
                    newPassword: this.newPassword,
                }).then(() => {
                    this.password = '';
                    this.newPassword = '';
                    this.confirmationPassword = '';
                }).catch(error => {
                    if (error && error.response && error.response.status === 422) {
                        this.errors.password = error.response.data.error;
                    }
                });
            }
        }
    },
    mounted() {
        this.name = this.$store.state.user.name;
        this.avatarUrl = this.$store.state.user.avatarUrl ? `https://nutriclock.s3-eu-west-1.amazonaws.com/avatars/${this.$store.state.user.avatarUrl}` : 'https://nutriclock.herokuapp.com/images/avatar.jpg';
        this.email = this.$store.state.user.email;
        this.role = renderRole(this.$store.state.user.role);
        this.gender = this.$store.state.user.gender;

        if (this.isFetching) return;
        this.isFetching = true;

        if (this.$store.state.user.role === UserRoles.Professional) {
            axios.get('api/ufcs/auth/description').then(response => {
                this.userUfcs = response.data.data;
            }).catch((error) => {
                if (error.response && error.response.status === 401) {
                    this.isFetching = false;
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            });
        }

        axios.get('api/professionalCategories').then(response => {
            this.isFetching = false;
            this.userCategory = getCategoryNameById(this.$store.state.user.professionalCategoryId, response.data.data);
        }).catch((error) => {
            this.isFetching = false;
            if (error.response && error.response.status === 401) {
                this.$store.commit('clearUserAndToken');
                this.$router.push({path: ROUTE.Login });
            }
        });
    },
    components: {
        FileUpload,
    }
};
</script>

<style>
.with-px-4 {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
}
@media only screen and (max-width: 600px) {
    .mobile-header-wrapper {
        flex-direction: column !important;
        display: flex;
        align-items: flex-start;
    }

    .with-px-4 {
        padding-left: 0.25rem !important;
        padding-right: 0.25rem !important;
    }
}
</style>
