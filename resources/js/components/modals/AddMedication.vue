<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">{{title}}</span>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="add-medication-modal-input-name" class="green-label">Nome</label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.name !== null }"
                                        id="add-medication-modal-input-name"
                                        v-model.trim="name"
                                    >
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{errors.name}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="add-medication-modal-input-posology" class="green-label">Posologia (mg/ml)</label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-bind:class="{ 'is-invalid': errors.posology !== null }"
                                        id="add-medication-modal-input-posology"
                                        v-model.trim="posology"
                                        placeholder='20'
                                    >
                                    <div v-if="errors.posology" class="invalid-feedback">
                                        {{errors.posology}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="form-group col-sm-6">
                                <label for="add-medication-modal-select-timesADay" class="green-label">Intervalo Horário</label>
                                <div>
                                    <select
                                        class="form-control"
                                        id="add-medication-modal-select-timesADay"
                                        v-model="selectedTimesADay">
                                        <option value="" disabled selected>Selecione um horário...</option>
                                        <option v-for="t in timesADay" :value="t.value">{{t.label}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="add-medication-modal-select-timesAWeek" class="green-label">Período de Administração</label>
                                <div>
                                    <select
                                        class="form-control"
                                        id="add-medication-modal-select-timesAWeek"
                                        multiple
                                        v-model="selectedTimesAWeek">
                                        <option value="" disabled selected>Selecione os períodos de administração...</option>
                                        <option v-for="t in timesAWeek" :value="t.value">{{t.label}}</option>
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
    import {
        ERROR_MESSAGES,
        isEmptyField,
    } from '../../utils/validations';


    export default{
        props: ['title', 'id', 'user_id'],
        data() {
            return {
                name: '',
                posology: '',
                selectedTimesADay: '',
                selectedTimesAWeek:[],
                medicationId: null,
                timesADay: [{
                    value: 'De 4 em 4h',
                    label: 'De 4 em 4h',
                }, {
                    value: 'De 6 em 6h',
                    label: 'De 6 em 6h',
                }, {
                    value: 'De 8 em 8h',
                    label: 'De 8 em 8h',
                }, {
                    value: 'De 12 em 12h',
                    label: 'De 12 em 12h',
                }, {
                    value: 'De 24 em 24h',
                    label: 'De 24 em 24h',
                }],
                timesAWeek: [{
                    value: 'ALL',
                    label: 'Todos os dias',
                }, {
                    value: '2',
                    label: 'Segunda',
                }, {
                    value: '3',
                    label: 'Terça',
                }, {
                    value: '4',
                    label: 'Quarta',
                }, {
                    value: '5',
                    label: 'Quinta',
                }, {
                    value: '6',
                    label: 'Sexta',
                }, {
                    value: '7',
                    label: 'Sábado',
                }, {
                    value: '1',
                    label: 'Domingo',
                },],
                errors: {
                    name: null,
                    posology: null,
                },
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
                this.errors.posology = null;

                if (isEmptyField(this.name)) {
                    this.errors.name = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isEmptyField(this.posology)) {
                    this.errors.posology = ERROR_MESSAGES.mandatoryField;
                    hasErrors = true;
                }

                if (isNaN(this.posology)) {
                    this.errors.posology = ERROR_MESSAGES.invalidFormat;
                    hasErrors = true;
                }

                if (hasErrors) {
                    return;
                }

                let strTimes = '';

                if (this.selectedTimesAWeek.includes('ALL')) {
                    strTimes='1,2,3,4,5,6,7,';
                } else {
                    this.selectedTimesAWeek.forEach(t => {
                        strTimes += `${t}, `;
                    });
                }

                this.isFetching = true;

                if (this.medicationId) {
                    axios.put(`api/medications/${this.medicationId}`, {
                        name: this.name,
                        posology: this.posology,
                        timesADay: this.selectedTimesADay,
                        timesAWeek: strTimes,
                    }).then(() => {
                        this.isFetching = false;
                        this.$emit('save');
                    }).catch(() => {
                        this.isFetching = false;
                        this.errors.name = ERROR_MESSAGES.medicationAlreadyExist;
                    });
                    return;
                }

                axios.post(`api/medications/${this.user_id}`, {
                    name: this.name,
                    posology: this.posology,
                    timesADay: this.selectedTimesADay,
                    timesAWeek: strTimes,
                }).then(() => {
                    this.isFetching = false;
                    this.$emit('save');
                }).catch(() => {
                    this.isFetching = false;
                    this.errors.name = ERROR_MESSAGES.medicationAlreadyExist;
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
            getMedication(id) {
                axios.get(`api/medication/${id}`).then(response => {
                    this.name = response.data.data.name;
                    this.posology = response.data.data.posology;
                    if (response.data.data.timesADay) {
                        this.selectedTimesADay = response.data.data.timesADay;
                    }
                    if (response.data.data.timesAWeek) {
                        const strArray = response.data.data.timesAWeek.replace(/,\s*$/, "").split(',');
                        strArray.forEach(s => {
                            this.selectedTimesAWeek.push(s.trim());
                        });
                    }
                }).catch();
            }
        },
        watch: {
            id: function(newVal, oldVal) {
                this.medicationId = newVal;
                this.name = '';
                this.posology = '';
                this.selectedTimesAWeek = [];
                this.selectedTimesADay = '';
                this.errors.name = null;
                this.errors.posology = null;
                if (newVal) {
                    this.getMedication(newVal)
                }
            }
        },
    };
</script>
