<template>
    <div class="component-wrapper">
        <div class="container with-pt-5 with-pb-2">
            <div class="with-p-4 bg-light rounded with-shadow">
                <div class="component-wrapper-header">
                    <h3 class="component-wrapper-left">
                        Indicadores Biométricos
                    </h3>
                    <div class="component-wrapper-right"/>
                </div>
                <div class="component-wrapper-body text-dark mt-2">
                    <div class="with-shadow mb-4 p-4">
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

                        <div v-if="samples.length === 0" class="with-shadow rounded p-2 my-2">
                            Não existem recolhas registadas
                        </div>
                        <div v-else v-for="(sample, index) in samples" :key="sample.id" class="with-shadow rounded p-2 my-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-primary text-break">{{ sample.name }}</div>
                                    <div class="mt-1 text-break">{{ sample.date }}</div>
                                </div>
                                <div class="text-secondary mr-2" v-show="index > 0"
                                     @click="() => moveUp(sample, 'SAMPLE')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-arrow-up" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                                    </svg>
                                </div>

                                <div class="text-secondary mr-2"
                                     v-show="index < samples.length - 1" @click="() => moveDown(sample, 'SAMPLE')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-arrow-down" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                    </svg>
                                </div>
                                <div @click="() => deleteItem(`api/biometric-collection/${sample.id}`)">
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
                            <div class="mr-1 font-weight-bold text-primary">{{ index + 1 }}.</div>
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
                            <div class="mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#409eff"
                                     class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                </svg>
                            </div>
                            <div @click="() => deleteItem(`api/biometric-procedure/${step.id}`)">
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
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import {ROUTE} from '../../utils/routes';

export default {
    data() {
        return {
            isFetching: false,
            showSampleModal: false,
            samples: [],
            procedureSteps: []
        };
    },
    methods: {
        deleteItem(url) {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.delete(url).then(response => {
                console.log(response.data.data);
                this.isFetching = false;
            }).catch(() => {
                this.isFetching = false;
            });
        },
        moveUp(item, type) {

        },
        moveDown(item, type) {

        },
        addSample() {
            this.showSampleModal = true;
        },
        addProcedureStep() {

        },
        closeModal() {
            this.showSampleModal = false;
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
    mounted() {
        this.getBiometricCollection();
        this.getProcedure();
    },
};
</script>
