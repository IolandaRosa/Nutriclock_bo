<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Biomarcadores
                    </h3>
                    <div class="component-wrapper-right"/>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <div class="with-shadow mb-4 p-4">
                        <div class="d-flex">
                            <div class="flex-grow-1 text-secondary font-weight-bold">Grupos de Recolha</div>
                            <button type="button" class="btn btn-outline-primary btn-sm mr-2" data-toggle="tooltip"
                                    title="Novo Grupo de Recolha" v-on:click="addGroup">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-plus" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                            </button>
                        </div>

                        <div v-if="groups.length === 0" class="with-shadow rounded p-2 my-2">
                            Não existem grupos de recolha
                        </div>
                        <div v-else v-for="(group, index) in groups" :key="group.id"
                             class="with-shadow rounded p-2 my-2">
                            <div class="text-primary text-break mb-4">{{ group.name }}</div>

                            <div class="d-flex">
                                <div class="flex-grow-1 text-secondary font-weight-bold">Recolhas</div>
                                <button type="button" class="btn btn-outline-primary btn-sm mr-2" data-toggle="tooltip"
                                        title="Nova Recolha" v-on:click="addSample">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-plus" viewBox="0 0 16 16">
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                </button>
                            </div>

                            <div v-if="group.collections.length === 0" class="with-shadow rounded p-2 my-2">
                                Não existem recolhas registadas
                            </div>
                            <div v-else v-for="(sample, index) in group.collections" :key="sample.id"
                                 class="with-shadow rounded p-2 my-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-primary text-break">{{ sample.name }}</div>
                                        <div class="mt-1 text-break">{{ sample.date }}</div>
                                    </div>
                                    <div class="text-secondary mr-2" v-show="index > 0"
                                         @click="() => moveUp(sample, 'SAMPLE')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor"
                                             class="bi bi-arrow-up" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                                        </svg>
                                    </div>

                                    <div class="text-secondary mr-2"
                                         v-show="index < samples.length - 1" @click="() => moveDown(sample, 'SAMPLE')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor"
                                             class="bi bi-arrow-down" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                        </svg>
                                    </div>
                                    <div @click="() => onDeleteClick(`api/biometric-collection/${sample.id}`)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f56c6c"
                                             class="bi bi-trash-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-secondary font-weight-bold">Intervalos Horários</div>
                                <div class="d-flex">
                                    <div v-for="interval in sample.intervals" class="font-weight-bold mr-2"
                                         style="font-size: 12px">
                                        {{ interval.hour }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="with-shadow p-4">
                        <div class="d-flex">
                            <div class="flex-grow-1 text-secondary font-weight-bold">Passos do Procedimento</div>
                            <button type="button" class="btn btn-outline-primary btn-sm mr-2" data-toggle="tooltip"
                                    title="Novo Passo" v-on:click="addProcedureStep">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-plus" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                            </button>
                        </div>
                        <div v-if="procedureSteps.length === 0" class="with-shadow rounded p-2 my-2">
                            Não existe procedimento registado
                        </div>
                        <div v-else v-for="(step, index) in procedureSteps" :key="step.id"
                             class="d-flex p-2 my-2 with-shadow rounded">
                            <div class="mr-1 font-weight-bold text-primary">{{ step.orderNumber + 1 }}.</div>
                            <div class="flex-grow-1 mr-2 text-break">{{ step.value }}</div>
                            <div class="text-secondary mr-2" v-show="index > 0" @click="() => moveUp(step, 'STEP')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-arrow-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                                </svg>
                            </div>
                            <div class="text-secondary mr-2" v-show="index < procedureSteps.length - 1"
                                 @click="() => moveDown(step, 'STEP')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-arrow-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                </svg>
                            </div>
                            <div @click="() => onDeleteClick(`api/biometric-procedure/${step.id}`)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f56c6c"
                                     class="bi bi-trash-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmationModal
            v-show="showConfirmation"
            @close="this.closeModal"
            title="Eliminar Item Selecionado"
            cancel-button-text="Cancelar"
            save-button-class="btn btn-bold btn-danger"
            save-button-text="Eliminar"
            @save="this.deleteItem"
            message="Tem a certeza que deseja eliminar o item selecionado?"
        />
        <AddSampleModal
            v-show="showSampleModal"
            @close="this.closeModal"
            @save="this.saveSample"
            :size="samples.length"/>
        <AddCategory
            v-show="showProcedureModal"
            @close="this.closeModal"
            :title="procedureModalTitle"
            selectedName="null"
            selectedId="null"
            :placeholder-name="procedureModalPlaceName"
            :max-char="200"
            @save="this.saveStep"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {ROUTE} from '../../utils/routes';
import AddCategory from '../modals/AddCategory';
import AddSampleModal from '../modals/AddSampleModal';
import ConfirmationModal from '../modals/ConfirmationModal';
import {startsWith} from 'lodash';

export default {
    data() {
        return {
            isFetching: false,
            showSampleModal: false,
            showProcedureModal: false,
            showConfirmation: false,
            selectedUrl: null,
            samples: [],
            procedureSteps: [],
            groups: [],
            procedureModalTitle: '',
            procedureModalPlaceName: '',
        };
    },
    methods: {
        saveStep(value, id) {
            this.closeModal();
            if (this.procedureModalTitle === 'Novo Passo de Procedimento') {
                axios.post('api/biometric-procedure', {
                    orderNumber: this.procedureSteps.length,
                    value: value,
                }).then((response) => {
                    this.procedureSteps = response.data.data;
                }).catch(() => {
                    this.showMessage('Ocorreu um erro', 'error');
                });
            } else {
                axios.post('api/biometric-group', {
                    name: value,
                }).then((response) => {
                    this.getCollectionGroups();
                }).catch(() => {
                    this.showMessage('Ocorreu um erro', 'error');
                });
            }
        },
        saveSample(data) {
            if (data) {
                this.samples = data;
            } else {
                this.showMessage('Ocorreu um erro', 'error');
            }
            this.closeModal();
        },
        showMessage(message, className) {
            this.$toasted.show(message, {
                type: className,
                duration: 3000,
                position: 'top-right',
                closeOnSwipe: true,
                theme: 'toasted-primary'
            });
        },
        onDeleteClick(url) {
            this.showConfirmation = true;
            this.selectedUrl = url;
        },
        deleteItem() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.delete(this.selectedUrl).then(response => {
                this.isFetching = false;
                this.closeModal();
                if (startsWith(this.selectedUrl, 'api/biometric-procedure')) {
                    this.procedureSteps = response.data.data;
                } else {
                    this.samples = response.data.data;
                }
                this.selectedUrl = null;
            }).catch(() => {
                this.showMessage('Ocorreu um erro', 'error');
                this.closeModal();
                this.isFetching = false;
                this.selectedUrl = null;
            });
        },
        moveUp(item, type) {
            let url = `api/biometric-collection-up/${item.id}`;
            if (type === 'STEP') {
                url = `api/biometric-procedure-up/${item.id}`;
            }
            this.reorderRequest(url, type);
        },
        moveDown(item, type) {
            let url = `api/biometric-collection-down/${item.id}`;
            if (type === 'STEP') {
                url = `api/biometric-procedure-down/${item.id}`;
            }
            this.reorderRequest(url, type);
        },
        reorderRequest(url, type) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get(url).then(response => {
                this.isFetching = false;
                if (type === 'STEP') {
                    this.procedureSteps = response.data.data;
                } else {
                    this.samples = response.data.data;
                }
            }).catch(() => {
                this.isFetching = false;
                this.showMessage('Ocorreu um erro', 'error');
            });
        },
        addGroup() {
            this.showProcedureModal = true;
            this.procedureModalTitle = 'Novo Grupo de Recolha';
            this.procedureModalPlaceName = 'Nome';
        },
        addSample() {
            this.showSampleModal = true;
        },
        addProcedureStep() {
            this.showProcedureModal = true;
            this.procedureModalTitle = 'Novo Passo de Procedimento';
            this.procedureModalPlaceName = 'Descrição';
        },
        closeModal() {
            this.showSampleModal = false;
            this.showProcedureModal = false;
            this.showConfirmation = false;
        },
        getCollectionGroups() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/biometric-group').then(response => {
                this.isFetching = false;
                console.log(response.data);
                this.groups = response.data.data;
            }).catch(error => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        },
        getBiometricCollection() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/biometric-collection').then(response => {
                this.isFetching = false;
                this.samples = response.data.data;
            }).catch(error => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        },
        getProcedure() {
            axios.get('api/biometric-procedure').then(response => {
                this.procedureSteps = response.data.data;
            }).catch(error => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        }
    },
    components: {
        AddSampleModal,
        AddCategory,
        ConfirmationModal,
    },
    mounted() {
        this.getCollectionGroups();
        this.getProcedure();
    },
};
</script>
