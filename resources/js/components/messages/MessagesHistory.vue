<template>
    <div class="component-wrapper">
        <div class="messages-container d-flex p-4 mobile">
            <div class="mr-2 p-3 text-dark bg-light rounded w-400 with-overflow mobile">
                <div class="font-weight-bold">Contactos</div>
                <div
                    v-for="item in contacts"
                    class="text-secondary pointer p-1 d-flex shadow justify-content-center align-items-center mb-2 bg-light border border-secondary rounded-sm mobile-container"
                    @click="() => { redirectMessages(item.senderId)}"
                >
                    <img
                        height="25"
                        width="25"
                        :src="'images/placeholder.jpg'"
                        alt=""
                        class="rounded-circle mr-1 hidden-image"
                    />
                    <div class="font-weight-bold mr-1 flex-grow-1 mobile-container-text">{{ item.senderName }}</div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-chat-left-quote-fill" viewBox="0 0 16 16">
                            <path
                                d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm7.194 2.766a1.688 1.688 0 0 0-.227-.272 1.467 1.467 0 0 0-.469-.324l-.008-.004A1.785 1.785 0 0 0 5.734 4C4.776 4 4 4.746 4 5.667c0 .92.776 1.666 1.734 1.666.343 0 .662-.095.931-.26-.137.389-.39.804-.81 1.22a.405.405 0 0 0 .011.59c.173.16.447.155.614-.01 1.334-1.329 1.37-2.758.941-3.706a2.461 2.461 0 0 0-.227-.4zM11 7.073c-.136.389-.39.804-.81 1.22a.405.405 0 0 0 .012.59c.172.16.446.155.613-.01 1.334-1.329 1.37-2.758.942-3.706a2.466 2.466 0 0 0-.228-.4 1.686 1.686 0 0 0-.227-.273 1.466 1.466 0 0 0-.469-.324l-.008-.004A1.785 1.785 0 0 0 10.07 4c-.957 0-1.734.746-1.734 1.667 0 .92.777 1.666 1.734 1.666.343 0 .662-.095.931-.26z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-3 text-dark bg-light rounded with-overflow mobile">
                <div v-if="!messages || messages.length === 0" class="text-center font-weight-bold mt-4">
                    Clica num contacto para veres o histórico de conversas
                </div>
                <div v-else>
                    <div v-for="item in messages">
                        <div
                            :class="[item.senderId === $store.state.user.id ? 'justify-content-end': 'justify-content-start', 'd-flex mb-2']"
                            style="font-size: 12px">
                            <div style="width: 90%"
                                 :class="[item.senderId === $store.state.user.id ? 'bg-secondary': 'bg-primary', 'text-light with-shadow rounded-sm p-2']">
                                <div class="d-flex mb-1 mobile-container">
                                    <div class="flex-grow-1 font-weight-bold d-flex align-items-center">
                                        <div v-show="item.senderId !== $store.state.user.id && !item.read"
                                             class="bg-danger rounded-circle mr-1" style="width: 10px; height: 10px"/>
                                        {{ item.senderName }}
                                    </div>
                                    <div class="font-weight-bold">
                                        {{ new Date(item.created_at).toLocaleString() }}
                                    </div>
                                </div>
                                <div>
                                    {{ item.message }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-dark text-light mt-2" v-show="showInputField">
                        <div class="card-header px-3 d-flex align-items-center py-2">
                            <div class="font-weight-bolder flex-grow-1">Escreva a sua mensagem...</div>
                            <button type="button" class="btn btn-sm btn-outline-light" @click="sendMessage">
                                Enviar
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M9.079 11.9l4.568-3.281a.719.719 0 0 0 0-1.238L9.079 4.1A.716.716 0 0 0 8 4.719V6c-1.5 0-6 0-7 8 2.5-4.5 7-4 7-4v1.281c0 .56.606.898 1.079.62z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="card-body py-2">
                            <textarea
                                v-bind:class="[errors.response !== null ? 'form-control is-invalid' : 'form-control' ]"
                                rows="7" v-model="response"></textarea>
                            <div v-if="errors.response" class="invalid-feedback">
                                {{ errors.response }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" v-show="!showInputField" class="btn btn-sm btn-outline-dark" @click="() => { this.showInputField = true; }">
                            Nova Mensagem
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-chat-quote-fill" viewBox="0 0 16 16">
                                <path
                                    d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM7.194 6.766a1.688 1.688 0 0 0-.227-.272 1.467 1.467 0 0 0-.469-.324l-.008-.004A1.785 1.785 0 0 0 5.734 6C4.776 6 4 6.746 4 7.667c0 .92.776 1.666 1.734 1.666.343 0 .662-.095.931-.26-.137.389-.39.804-.81 1.22a.405.405 0 0 0 .011.59c.173.16.447.155.614-.01 1.334-1.329 1.37-2.758.941-3.706a2.461 2.461 0 0 0-.227-.4zM11 9.073c-.136.389-.39.804-.81 1.22a.405.405 0 0 0 .012.59c.172.16.446.155.613-.01 1.334-1.329 1.37-2.758.942-3.706a2.466 2.466 0 0 0-.228-.4 1.686 1.686 0 0 0-.227-.273 1.466 1.466 0 0 0-.469-.324l-.008-.004A1.785 1.785 0 0 0 10.07 6c-.957 0-1.734.746-1.734 1.667 0 .92.777 1.666 1.734 1.666.343 0 .662-.095.931-.26z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */

import { ROUTE } from '../../utils/routes';
import { ERROR_MESSAGES, isEmptyField } from '../../utils/validations';
import { makeSocketEvent, parseSocketMessage } from '../../utils/misc';
import { EventType } from '../../constants/misc';

export default {
    data() {
        return {
            contacts: [],
            messagesHistory: [],
            messages: [],
            response: null,
            showInputField: false,
            isFetching: false,
            errors: {
                response: null,
            }
        };
    },
    methods: {
        redirectMessages(id) {
            if (id !== this.$route.params.id) {
                this.$router.push({
                    name: 'MessagesHistory',
                    params: {
                        id: id,
                    }
                });
            }
        },
        sendMessage() {
            if (isEmptyField(this.response)) {
                this.errors.response = ERROR_MESSAGES.mandatoryField;
                return
            }

            if (this.isFetching) return;
            this.isFetching = true;

            const selectedContact = this.getContactByParamId();
            const dataToSend = {
                senderId: this.$store.state.user.id,
                senderName: this.$store.state.user.name,
                senderPhotoUrl: this.$store.state.user.avatarUrl,
                receiverId: selectedContact.senderId,
                receiverName: selectedContact.senderName,
                receiverPhotoUrl: selectedContact.senderPhotoUrl,
                message: this.response,
                refMessageId: this.getRefMessageId(),
                read: false,
                fromModal: false,
            }

            if (selectedContact) {
                axios.post('api/messages', dataToSend).then(() => {
                    this.isFetching = false;
                    this.showInputField = false;
                    this.response = '';
                    this.errors.response = null;
                    this.$socket.send(makeSocketEvent(EventType.Store, dataToSend));
                }).catch(() => {
                    this.isFetching = false;
                    this.showMessage('Ocorreu um erro ao enviar a mensagem', 'error');
                });
            }
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
        getMessages() {
            if (this.isFetching) return;
            this.isFetching = true;

            axios.get('api/messages').then(response => {
                this.isFetching = false;
                if (response.data.contacts) {
                    this.contacts = response.data.contacts;
                }

                if (response.data.messagesHistory) {
                    this.messagesHistory = response.data.messagesHistory;

                    if (this.$route.params.id) {
                        this.messages = this.messagesHistory[this.$route.params.id];
                    }
                }
            }).catch((error) => {
                this.isFetching = false;
                if (error.response && error.response.status === 401) {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login});
                }
            });
        },
        getContactByParamId() {
            let selectedContact = null;
            if (this.$route.params.id) {
                this.contacts.forEach(c => {
                    if (Number(c.senderId) === Number(this.$route.params.id)) {
                        selectedContact = c;
                    }
                })
            }
            return selectedContact;
        },
        getRefMessageId() {
            let refId = null;
            if (this.messages && this.messages.length > 0) {
                refId = this.messages[this.messages.length -1].id;
            }
            return refId;
        }
    },
    mounted() {
        this.getMessages();
        this.$options.sockets.onmessage = (data) => {
            if (data && data.data) {
                const message = parseSocketMessage(data.data);

                if (Number(message.senderId) === this.$store.state.user.id
                || Number(message.receiverId) === this.$store.state.user.id) {
                    this.getMessages();
                    axios.get('/api/messages/unread-count').then(response => {
                        const { data } = response;
                        this.$store.commit('setUnread', data.data);
                    }).catch(() => {})
                }
            }
        }
    },
    watch: {
        '$route.params.id': function () {
            this.messages = this.messagesHistory[this.$route.params.id];
        }
    },
}
</script>

<style>
.messages-container {
    position: absolute;
    bottom: 0;
    top: 60px;
}

.with-overflow {
    overflow-y: auto;
}

.w-400 {
    width: 400px;
}

.hidden-image {
    object-fit: cover;
    display: unset;
}

@media only screen and (max-width: 600px) {
    .w-400 {
        max-width: 100px;
    }

    .mobile-container {
        flex-direction: column;
        align-items: center;
        font-size: 12px;
    }

    .mobile {
        margin-right: 0.25rem !important;
        padding: 0.25rem !important;
        font-size: 12px;
    }

    .mobile-container-text {
        text-align: center;
    }
}
</style>
