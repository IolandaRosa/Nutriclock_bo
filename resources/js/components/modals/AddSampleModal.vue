<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" style="max-width: 400px; min-height: 500px;">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Nova Recolha</span>
                    </div>
                    <div class="modal-body m-0">
                        <div class="form-group">
                            <label for="add-sample-modal-input-name" class="green-label">Nome</label>
                            <div>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-bind:class="{ 'is-invalid': errors.name !== null }"
                                    id="add-sample-modal-input-name"
                                    v-model.trim="name"
                                >
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="green-label">Data</label>
                            <Datepicker
                                v-model="date"
                                input-class="form-control"
                                calendar-class="text-secondary"
                                :disabledDates="disabledDates"
                                :language="pt"
                            />
                            <div v-if="errors.date" class="invalid-feedback">
                                {{ errors.date }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="green-label">Adicionar intervalo horário</label>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 mr-4">
                                    <input class="form-control"
                                           v-bind:class="{ 'is-invalid': errors.time !== null }"
                                           type="time" v-model="time">
                                    <div v-if="errors.time" class="invalid-feedback">
                                        {{ errors.time }}
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" @click="addTime">
                                        Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="green-label">Intervalos Horários Adicionados</div>
                        <div class="text-secondary font-weight-bold">
                            <div v-for="(h, index) in hours" :key="h">
                                <span>{{ h }}</span>
                                <span @click="() => removeItem(index)" class="text-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-0">
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
import Datepicker from 'vuejs-datepicker';
import {ptBR} from 'vuejs-datepicker/dist/locale';
import {parseDateToString} from '../../utils/misc';
import {ERROR_MESSAGES, isEmptyField} from '../../utils/validations';

export default {
    props: ['size'],
    data() {
        return {
            name: '',
            date: new Date(),
            time: '',
            hours: [],
            errors: {
                name: null,
                date: null,
                time: null,
            },
            disabledDates: {
                to: new Date(Date.now() - 86400000),
            },
            pt: ptBR,
        };
    },
    methods: {
        addTime() {
            this.resetErrors();
            if (isEmptyField(this.time)) {
                this.errors.time = ERROR_MESSAGES.mandatoryField;
                return;
            }

            if(this.hours.includes(this.time)) return;

            this.hours.push(this.time);
        },
        removeItem(index) {
            this.hours.splice(index, 1);
        },
        onCloseClick() {
            this.resetErrors();
            this.resetFields();
            this.$emit('close');
        },
        resetFields() {
            this.name = '';
            this.date = new Date();
            this.time = '';
            this.hours = [];
        },
        resetErrors() {
            this.errors.name = null;
            this.errors.date = null;
            this.errors.time = null;
        },
        onSaveClick() {
            this.resetErrors();

            if (isEmptyField(this.name)) {
                this.errors.name = ERROR_MESSAGES.mandatoryField;
                return;
            }

            if (isEmptyField(this.date)) {
                this.errors.date = ERROR_MESSAGES.mandatoryField;
                return;
            }

            if (this.hours.length === 0) {
                this.errors.time = ERROR_MESSAGES.mandatoryField;
                return;
            }

            axios.post('api/biometric-collection', {
                orderNumber: this.size,
                name: this.name,
                date: parseDateToString(this.date),
                intervals: this.hours,
            }).then(response => {
                this.resetFields();
                this.$emit('save',  response.data.data);
            }).catch(() => {
                this.resetFields();
                this.$emit('save', null);
            });
        },
    },
    components: {
        Datepicker,
    }
};
</script>
