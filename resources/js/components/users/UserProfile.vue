<template>
    <div class="component-wrapper">
        <div class="container pt-5">
            <div class="p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <div class="component-wrapper-left"/>
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
                <div class="profile-wrapper-header">
                    <FileUpload
                        :image-url="avatarUrl"
                        @onFileSelected="this.onFileSelected"
                        :disabled="false"
                    />
                    <div class="mt-2 mb-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="text-secondary small-text">{{ role }}</div>
                        <div class="text-secondary small-text">{{ userCategory }}</div>
                    </div>
                </div>
                <div class="component-wrapper-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="name" class="small-text text-secondary">Nome</label>
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
                        <div class="form-group col-md-4">
                            <label for="user-profile-input-email" class="small-text text-secondary">Email</label>
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
                        <div class="form-group col-md-4">
                            <label class="small-text text-secondary">Género</label>
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
                    <div>
                        <h4 class="text-secondary mb-2 mt-4">Alteração de Password</h4>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="user-profile-password" class="small-text text-secondary">Password</label>
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
                            <div class="form-group col-md-4">
                                <label for="user-profile-new-password" class="small-text text-secondary">Nova
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
                            <div class="form-group col-md-4">
                                <label for="user-profile-password" class="small-text text-secondary">Confirmação de
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
</template>

<script type="text/javascript">
import FileUpload from '../utils/FileUpload';
import {getCategoryNameById, renderRole} from "../../utils/misc";
import {equalFields, ERROR_MESSAGES, isEmailFormatInvalid, isEmptyField} from "../../utils/validations";
import {ROUTE} from "../../utils/routes";

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
        this.avatarUrl = this.$store.state.user.avatarUrl ? `https://nutriclock.s3-eu-west-1.amazonaws.com/avatars/${this.$store.state.user.avatarUrl}` : 'https://nutriclock.herokuapp.com/images/avatar.png';
        this.email = this.$store.state.user.email;
        this.role = renderRole(this.$store.state.user.role);
        this.gender = this.$store.state.user.gender;

        if (this.isFetching) return;
        this.isFetching = true;

        axios.get('api/professionalCategories').then(response => {
            this.isFetching = false;
            this.userCategory = getCategoryNameById(this.$store.state.user.professionalCategoryId, response.data.data);
        }).catch(() => {
            this.isFetching = false;
            if (error.response && error.response.status === 401) {
                this.$router.push(ROUTE.Login)
            }
        });
    },
    components: {
        FileUpload,
    }
};
</script>

<style>
@media only screen and (max-width: 600px) {
    .mobile-header-wrapper {
        flex-direction: column !important;
        display: flex;
        align-items: flex-start;
    }
}
</style>
