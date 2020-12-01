<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" src="http://nutriclock.test:81/images/only_logo.png" alt=""/>
                    </div>
                    <div class="modal-header">
                        <div class="title">
                            Perfil de {{name}}
                        </div>
                    </div>
                    <div class="modal-body">
                        <FileUpload
                            :image-url="avatarUrl"
                            @onFileSelected="() => {}"
                            :disabled="userId !== -1"
                        />
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="profile-modal-input-name" class="green-label">Nome</label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="profile-modal-input-name"
                                        v-model.trim="name"
                                        placeholder="Nome"
                                        :readonly="userId !== -1"
                                    >
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="profile-modal-input-email" class="green-label">Email</label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="profile-modal-input-email"
                                        readonly=""
                                        :value="email"
                                        placeholder="Email"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2" v-show="userUfcs.lenght > 0">
                            <label class="green-label">Unidade de Saúde Familiar</label>
                            <div>
                                <input
                                    type="text"
                                    class="form-control mt-1"
                                    readonly=""
                                    v-for="ufs in userUfcs" :value="ufs.name"
                                    placeholder="USF"
                                >
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="profile-modal-select-role" class="green-label">Perfil de Utilizador</label>
                                <div>
                                    <select
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.role !== null }"
                                        id="profile-modal-select-role"
                                        v-model="role">
                                        <option v-for="role in roles" :value="role.value">{{role.label}}</option>
                                    </select>
                                </div>
                                <div v-if="errors.role" class="invalid-feedback">
                                    {{errors.role}}
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="profile-modal-select-category" class="green-label">Categoria Profissional</label>
                                <div>
                                    <div>
                                        <select
                                            class="form-control"
                                            id="profile-modal-select-category"
                                            v-model="professionalCategoryId">
                                            <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
                                        </select>
                                    </div>
                                    <div v-if="errors.professionalCategoryId" class="invalid-feedback">
                                        {{errors.professionalCategoryId}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-2 col-sm-12" v-show="role === 'PROFESSIONAL'">
                                <label for="profile-modal-select-ufc" class="green-label">Unidade de Saúde Familiar</label>
                                <div>
                                    <select
                                        class="form-control"
                                        id="profile-modal-select-ufc"
                                        multiple
                                        v-model="userUfcs">
                                        <option v-for="ufs in ufcs" :value="ufs.id">{{ufs.name}}</option>
                                    </select>
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
    import { ERROR_MESSAGES, isEmptyField } from '../../utils/validations';
    import FileUpload from '../utils/FileUpload';

    export default{
        props:['userId'],
        data() {
            return {
                name: '',
                role: '',
                email: '',
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
                ufcs: [],
                categories: [],
                userUfcs: [],
                professionalCategoryId: '',
                avatarUrl: '',
                id: null,
                errors: {
                    userUfcs: null,
                    professionalCategoryId: null,
                    role: null,
                }
            };
        },
        methods:{
            onCloseClick() {
                this.$emit('close');
            },
            onSaveClick() {
                this.errors.role = null;
                this.errors.professionalCategoryId = null;
                let hasErrors = false;

                if (isEmptyField(this.role)) {
                    this.errors.role = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmptyField(this.professionalCategoryId)) {
                    this.errors.professionalCategoryId = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (hasErrors) return;

                this.$emit('save', this.id, {
                    role: this.role,
                    professionalCategoryId: this.professionalCategoryId,
                    ufcs: this.userUfcs,
                });
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
            this.getUFS();
        },
        components: {
            FileUpload,
        },
        watch: {
            userId: function(newVal, oldVal) {
                if (newVal && newVal !== -1) {
                    this.id = newVal;
                    axios.get(`api/users/${this.id}/ufcs`).then(response => {
                        const array = [];
                        if (response.data.data && response.data.data.length > 0) {
                            response.data.data.forEach(d => {
                                array.push(d.id);
                            })
                        }
                        this.userUfcs = array;
                    }).catch(() => {});

                    axios.get(`api/users/${this.id}`).then(response => {
                        const user = response.data.data;
                        this.name = user.name;
                        this.email = user.email;
                        this.avatarUrl = user.avatarUrl ? 'https://nutriclock.s3-eu-west-1.amazonaws.com/avatars/'+ user.avatarUrl : 'https://nutriclock.herokuapp.com/images/avatar.png';
                        this.professionalCategoryId = user.professionalCategoryId;
                        this.role = user.role;
                        this.email = user.email;
                    }).catch(()=>{});
                }
            },
        }
    };
</script>
