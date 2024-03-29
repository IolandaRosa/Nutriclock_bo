<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-body text-dark" style="margin: 0">
                        <div v-if="message !== null" class="card bg-primary text-light" @click="() => { this.showMessageSend = !this.showMessageSend }">
                            <div class="card-header px-3 d-flex align-items-center">
                                <div class="pointer font-weight-bolder flex-grow-1">Messagem Recebida</div>
                                <svg v-show="!showMessageSend" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                                    <path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                                </svg>
                                <svg v-show="showMessageSend" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                                    <path d="M12.14 8.753l-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </div>
                            <div class="card-body" v-if="showMessageSend && selectedMessage">
                                <strong>{{  selectedMessage.senderName }}</strong>
                                <p class="card-text">{{ selectedMessage.message }}</p>
                            </div>
                        </div>
                        <div class="card bg-dark text-light mt-2">
                            <div class="card-header px-3 d-flex align-items-center py-2">
                                <div class="font-weight-bolder flex-grow-1">Escreva a sua resposta...</div>
                            </div>
                            <div class="card-body py-2">
                                <textarea v-bind:class="[errors.response !== null ? 'form-control is-invalid' : 'form-control' ]" rows="7" v-model="response"></textarea>
                                <div v-if="errors.response" class="invalid-feedback">
                                    {{errors.response}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 0 0.75rem 0 0">
                        <button class="btn-bold btn btn-dark" @click="onSaveClick">
                            Enviar Resposta
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
import { makeSocketEvent } from '../../utils/misc';
import { EventType } from '../../constants/misc';

export default {
    props: ['message', 'user'],
    data() {
        return {
            selectedMessage: null,
            showMessageSend: true,
            response: null,
            isFetching: false,
            errors: {
                response: null,
            }
        };
    },
    methods: {
        onCloseClick() {
            this.resetErrors();
            this.resetFields();
            this.$emit('close');
        },
        resetFields() {
            this.selectedMessage = null;
            this.showMessageSend = true;
            this.response = null;
        },
        resetErrors() {
            this.errors.response = null;
        },
        onSaveClick() {
            this.resetErrors();
            if (isEmptyField(this.response)) {
                this.errors.response = ERROR_MESSAGES.mandatoryField;
                return
            }

            if (this.isFetching) return;
            this.isFetching = true;
            const receiverId = this.selectedMessage ? this.selectedMessage.senderId : this.user.id;
            const receiverName = this.selectedMessage ? this.selectedMessage.senderName : this.user.name;
            const receiverPhotoUrl = this.selectedMessage ? this.selectedMessage.senderPhotoUrl : this.user.avatarUrl;
            const dataToSend = {
                senderId: this.$store.state.user.id,
                senderName: this.$store.state.user.name,
                senderPhotoUrl: this.$store.state.user.avatarUrl,
                receiverId,
                receiverName,
                receiverPhotoUrl,
                message: this.response,
                refMessageId: this.selectedMessage ? this.selectedMessage.id : -1,
                read: false,
                fromModal: false,
            };

            axios.post('api/messages', dataToSend).then(() => {
                this.isFetching = false;
                this.$socket.send(makeSocketEvent(EventType.Store, dataToSend));
                this.$emit('save');
                this.resetFields();
            }).catch(() => {
                this.isFetching = false;
                this.showMessage('Ocorreu um erro ao enviar a mensagem', 'error');
            });
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
    },
    watch: {
        message: function (newVal, oldVal) {
            this.selectedMessage = newVal;
        },
    },
};
</script>
