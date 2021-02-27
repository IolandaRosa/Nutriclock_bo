<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" style="max-width: 400px">
                    <div class="modal-logo">
                        <img class="modal-logo" :src="'images/only_logo.png'" alt=""/>
                    </div>
                    <div class="modal-header">
                        <span class="title">Nova Data</span>
                    </div>
                    <div class="modal-body m-0">
                        <div class="card-text">
                            <div class="form-group d-flex flex-column align-items-center">
                                <label class="small-text text-secondary">Selecione a Data</label>
                                <Datepicker
                                    v-model="date"
                                    input-class="form-control"
                                    calendar-class="text-secondary"
                                    :disabledDates="disabledDates"
                                    :language="pt"
                                    inline
                                />
                                <div v-if="errors.date" class="invalid-feedback">
                                    {{ errors.date }}
                                </div>
                            </div>
                        </div>
                        <div class="text-secondary font-weight-bold text-md-center mr-2 text-uppercase">
                            {{formatDate(date)}}
                        </div>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-center">
                        <button class="btn-bold btn btn-primary" @click="onSaveClick" style="width: 100px">
                            Ok
                        </button>
                        <button class="btn-bold btn btn-secondary" @click="onCloseClick" style="width: 100px">
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
import { ptBR } from 'vuejs-datepicker/dist/locale'
import { ERROR_MESSAGES, isEmptyField } from '../../utils/validations';
import { renderDate, renderDayOfWeek } from '../../utils/misc';

export default {
    data() {
        return {
            date: new Date(),
            errors: {
                date: null,
            },
            disabledDates: {
                to: new Date(Date.now() - 86400000),
            },
            pt: ptBR,
        };
    },
    methods: {
        onCloseClick() {
            this.resetErrors();
            this.resetFields();
            this.$emit('close');
        },
        resetFields() {
            this.date = new Date();
        },
        resetErrors() {
            this.errors.date = null;
        },
        formatDate(date) {
            return renderDayOfWeek(date.getDay())+" - "+renderDate(date);
        },
        onSaveClick() {
            this.resetErrors();
            if (isEmptyField(this.date)) {
                this.errors.date = ERROR_MESSAGES.mandatoryField;
                return;
            }
            const dayString = renderDayOfWeek(this.date.getDay());
            const dateString = renderDate(this.date);
            // todo post new date
            this.$emit('save', dayString+" - "+dateString);
            this.resetFields();
        },
    },
    components: {
        Datepicker,
    }
};
</script>
