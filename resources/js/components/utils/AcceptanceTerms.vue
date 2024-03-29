<template>
    <div class="component-wrapper">
        <div class="container with-pt-5">
            <div class="p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Termos de Aceitação
                    </h3>
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
                <div class="component-wrapper-body text-dark">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="title" class="white-label">Título</label>
                            <input
                                type="text"
                                class="form-control"
                                id="title"
                                v-bind:class="{ 'is-invalid': errors.title !== null }"
                                v-model="title"
                            >
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.title }}
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="version" class="white-label">Versão</label>
                            <input
                                type="text"
                                class="form-control"
                                id="version"
                                :value="version"
                                disabled
                            >
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="description" class="white-label">Descrição</label>
                            <textarea
                                class="form-control"
                                id="description"
                                v-model="description"
                                v-bind:class="{ 'is-invalid': errors.description !== null }"
                                rows="20"></textarea>
                            <div v-if="errors.description" class="invalid-feedback">
                                {{ errors.description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
import {ERROR_MESSAGES, isEmptyField} from '../../utils/validations';
import {ROUTE} from '../../utils/routes';

export default {
    data: function () {
        return {
            title: '',
            version: '',
            description: '',
            errors: {
                title: null,
                description: null,
            },
            isFetching: false,
        };
    },
    methods: {
        save() {
            this.errors.title = null;
            this.errors.description = null;
            if (isEmptyField(this.title)) {
                this.errors.title = ERROR_MESSAGES.mandatoryField;
                return;
            }

            if (isEmptyField(this.description)) {
                this.errors.description = ERROR_MESSAGES.mandatoryField;
                return;
            }

            if (this.isFetching) return;
            this.isFetching = true;

            axios.put(`api/terms/${this.version}`, {
                title: this.title,
                description: this.description,
            }).then(() => {
                this.isFetching = false;
                this.getTermsAndConditions();
                this.$toasted.show('A informação foi atualizada com sucesso!', {
                    type: 'success',
                    duration: 3000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
            }).catch(error => {
                this.isFetching = false;
                this.$toasted.show(ERROR_MESSAGES.unknownError, {
                    type: 'error',
                    duration: 3000,
                    position: 'top-right',
                    closeOnSwipe: true,
                    theme: 'toasted-primary'
                });
                console.log(error);
            })

        },
        getTermsAndConditions() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/terms').then(response => {
                this.isFetching = false;
                this.title = response.data.data.title;
                this.description = response.data.data.description;
                this.version = response.data.data.version;
            }).catch(error => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }
            });
        }
    },
    mounted() {
        this.getTermsAndConditions();
    },
};
</script>

