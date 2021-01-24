<template>
    <div>
        <div class="message-sidebar ">
            <div class="text-secondary w-100 d-flex align-items-end justify-content-end" @click="closeSidebarMessage">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x"
                     viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0
                    0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </div>
            <h5 class="text-dark mt-2">
                <strong>Messagens Recebidas</strong>
            </h5>

            <div class="overflow-auto">
                <div class="mt-4 text-dark d-flex flex-column justify-content-center text-center"
                     v-if="data && data.length === 0">
                    Não existem mensagens por ler
                    <button class="btn btn-link btn-sm" @click="() => { redirectToMessageHistory(null) }">
                        Ver histórico de mensagens
                    </button>
                </div>
                <div class="card text-white bg-primary mt-2" v-for="(item, index) in data" :key="item.id">
                    <div class="card-header d-flex justify-content-end">
                        <button class="btn btn-outline-light mr-1"
                                @click="() => { redirectToMessageHistory(item.senderId) }">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
                                <path
                                    d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                        </button>
                        <button class="btn btn-outline-light" @click="() => { replyMessage(index) }">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-reply-fill" viewBox="0 0 16 16">
                                <path
                                    d="M9.079 11.9l4.568-3.281a.719.719 0 0 0 0-1.238L9.079 4.1A.716.716 0 0 0 8 4.719V6c-1.5 0-6 0-7 8 2.5-4.5 7-4 7-4v1.281c0 .56.606.898 1.079.62z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body">
                        <strong>{{ item.senderName }}</strong>
                        <div class="mb-2">{{ item.cropedMessage }}</div>
                        <div class="d-flex justify-content-end mb-2">
                            <button class="btn btn-outline-light btn-sm" @click="() => { markAsRead(item.id) }">
                                Marcar como lida
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ChatResponseModal
            :message="selectedMessage"
            v-show="showReplyModal"
            @close="() => { this.showReplyModal = false }"
            @save="onNewResponseClick"
        />
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */
import ChatResponseModal from '../modals/ChatResponseModal';
import { ROUTE } from '../../utils/routes';
import { parseSocketMessage } from '../../utils/misc';

export default {
    data() {
        return {
            data: [],
            showReplyModal: false,
            selectedMessage: null,
            isFetching: false,
        };
    },
    methods: {
        closeSidebarMessage() {
            this.$emit('close-sidebar-message');
        },
        replyMessage(index) {
            this.showReplyModal = true;
            this.selectedMessage = this.data[index];
        },
        redirectToMessageHistory(id) {
            this.closeSidebarMessage();
            this.$router.push({
                name: 'MessagesHistory',
                params: {
                    id: id,
                }
            });
        },
        getUnreadMessages() {
            axios.get('api/messages/unread').then(response => {
                Object.keys(response.data.data).forEach(key => {
                    if (response.data.data[key].message.length > 80) {
                        response.data.data[key].cropedMessage = response.data.data[key].message.substring(0, 80) + "...";
                    } else {
                        response.data.data[key].cropedMessage = response.data.data[key].message;
                    }
                });

                this.data = response.data.data;
                if (this.$store.state.unread !== response.data.data.length) this.$store.commit('setUnread', response.data.data.length);
            }).catch(error => {
                if (error.response && error.response.status === 401) {
                    this.$router.push(ROUTE.Login)
                }
            });
        },
        markAsRead(id) {
            if (this.isFetching) return;

            this.isFetching = true;

            axios.put(`api/messages/read/${id}`).then(() => {
                this.isFetching = false;
                const value = this.$store.state.unread - 1;
                this.$store.commit('setUnread', value);
                this.getUnreadMessages();
            }).catch(() => {
                this.isFetching = false;
            })
        },
        onNewResponseClick() {
            this.showReplyModal = false;
            this.getUnreadMessages();
        }
    },
    mounted() {
        if (this.$store.state.unread > 0) this.getUnreadMessages();

        this.$options.sockets.onmessage = (data) => {
            if (data && data.data) {
                const message = parseSocketMessage(data.data);
                if (Number(message.receiverId) === this.$store.state.user.id) {
                    this.getUnreadMessages()
                }
            }
        }
    },
    unmounted() {
        delete this.$options.sockets.onmessage;
    },
    components: {
        ChatResponseModal,
    },
};
</script>

<style>
.message-sidebar {
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 0;
    background: #fafafa;
    right: 0;
    height: 100vh;
    width: 500px;
    padding: 24px;
    box-shadow: 0 3px 6px #0f0f0f28;
}

.bg-primary {
    background-color: #4540b0 !important;
}

.card-header {
    padding: 0.15rem 0.25rem;
}

.card-body {
    padding: 0.15rem 1.25rem;
}

@media only screen and (max-width: 900px) {
    .message-sidebar {
        width: 300px;
    }
}

@media only screen and (max-width: 600px) {
    .message-sidebar {
        width: 100%;
    }
}
</style>
