<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" src="http://nutriclock.test:81/images/only_logo.png" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Registar Utilizador</span>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="add-professional-modal-input-name" class="green-label">Nome</label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.name !== null }"
                                        id="add-professional-modal-input-name"
                                        v-model.trim="name"
                                        placeholder='Nome'
                                    >
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{errors.name}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="add-professional-modal-input-email" class="green-label">Email</label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.email !== null }"
                                        id="add-professional-modal-input-email"
                                        v-model.trim="email"
                                        placeholder='Email'
                                    >
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{errors.email}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div :class="selectedRole === 'PROFESSIONAL' ? 'form-group col-sm-6' : 'form-group col-sm-12'">
                                <label for="add-professional-modal-select-role" class="green-label">Perfil de Utilizador</label>
                                <div>
                                    <select
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.selectedRole !== null }"
                                        id="add-professional-modal-select-role"
                                        v-model="selectedRole">
                                        <option value="" disabled selected>Selecione um perfil...</option>
                                        <option v-for="role in roles" :value="role.value">{{role.label}}</option>
                                    </select>
                                    <div v-if="errors.selectedRole" class="invalid-feedback">
                                        {{errors.selectedRole}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-6" v-show="selectedRole === 'PROFESSIONAL'">
                                <label for="add-professional-modal-select-category" class="green-label">Categoria Profissional</label>
                                <div>
                                    <select
                                        class="form-control"
                                        id="add-professional-modal-select-category"
                                        v-model="selectedProfessionalCategory">
                                        <option value="" disabled selected>Selecione uma categoria...</option>
                                        <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2" v-show="selectedRole === 'PROFESSIONAL'">
                            <label for="add-professional-modal-select-ufc" class="green-label">Unidade de Saúde Familiar</label>
                            <div>
                                <select
                                    class="form-control"
                                    id="add-professional-modal-select-ufc"
                                    multiple
                                    v-model="selectedUfcs">
                                    <option value="" disabled selected>Selecione uma ou mais USF...</option>
                                    <option v-for="ufs in ufcs" :value="ufs.id">{{ufs.name}}</option>
                                </select>
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
    import {
        ERROR_MESSAGES,
        isEmailFormatInvalid,
        isEmptyField,
        isStringBiggerThanMax,
        isStringLowerThanMin
    } from '../../utils/validations';


    export default{
        data() {
            return {
                name: '',
                email: '',
                selectedRole: '',
                selectedProfessionalCategory:'',
                categories: [],
                roles: [{
                    value: 'ADMIN',
                    label: 'Administrador',
                }, {
                    value: 'INTERN',
                    label: 'Investigador',
                }, {
                    value: 'PROFESSIONAL',
                    label: 'Profissional de Saúde',
                }],
                errors: {
                    name: null,
                    email: null,
                    selectedRole: null,
                },
                ufcs: [],
                selectedUfcs: [],
            };
        },
        methods:{
            onCloseClick() {
                if (this.isFetching) return;
                this.$emit('close');
            },
            onSaveClick() {
                if (this.isFetching) return;

                let hasErrors = false;
                this.errors.name = null;
                this.errors.email = null;
                this.errors.selectedRole = null;

                if (isEmptyField(this.name)) {
                    this.errors.name = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmptyField(this.email)) {
                    this.errors.email = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmptyField(this.selectedRole)) {
                    this.errors.selectedRole = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (hasErrors) {
                    return;
                }

                if(isStringLowerThanMin(this.name, 3)) {
                    this.errors.name = `${ERROR_MESSAGES.minCharacters} 3 caratéres`;
                    hasErrors = true;
                }

                if(isStringBiggerThanMax(this.name, 40)) {
                    this.errors.name = `${ERROR_MESSAGES.maxCharacters} 40 caratéres`;
                    hasErrors = true;
                }

                if (isEmailFormatInvalid(this.email)){
                    this.errors.email = ERROR_MESSAGES.invalidFormat;
                    hasErrors = true;
                }

                if (hasErrors) return;

                axios.post('api/users/register', {
                    name: this.name,
                    email: this.email,
                    role: this.selectedRole,
                    professionalCategoryId: this.selectedProfessionalCategory,
                    usfIds: this.selectedUfcs
                }).then(() => {
                    this.sendEmail();
                    this.$emit('save');
                }).catch(error => {
                    this.isFetching = false;
                    const { response } = error;
                    if (response) {
                        const { data } = response;
                        if (data.errors.email) {
                            this.errors.email = data.errors.email[0];
                        }
                    } else {
                        this.errors.email = ERROR_MESSAGES.unknownError;
                    }
                });
            },
            sendEmail() {
                axios.post('api/password', {
                    email: this.email,
                    register: true,
                }).then(() => {
                    this.isFetching = false;
                    this.$toasted.show('O utilizador foi criado com sucesso', {
                        type: 'success',
                        duration: 3000,
                        position: 'top-right',
                        closeOnSwipe: true,
                        theme: 'toasted-primary'
                    });
                }).catch(() => {});
            },
            getCategories() {
                this.isFetching = true;
                axios.get('api/professionalCategories').then(response => {
                    this.isFetching = false;
                    this.categories = response.data.data;
                }).catch(() => {
                    this.isFetching = false;
                });
            },
            getUFS() {
                this.isFetching = true;
                axios.get('api/ufcs').then((response) => {
                    this.isFetching = false;
                    this.ufcs = response.data.data;
                }).catch(() => {
                    this.isFetching = false;
                });
            }
        },
        mounted() {
            this.getCategories();
            this.getUFS()
        }
    };
</script>
