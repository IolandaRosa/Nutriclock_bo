<template>
    <div class="component-wrapper">
        <div class="messages-container d-flex p-4 mobile">
            <div class="mr-2 p-3 text-dark bg-light rounded w-400 with-overflow mobile">
                <div class="font-weight-bold">Contactos</div>
                <div
                    v-for="item in contacts"
                    class="text-secondary pointer p-1 d-flex shadow justify-content-center align-items-center mb-2 bg-light border border-secondary rounded-sm mobile-container"
                    @click="() => { setSelectedMessages(item.id)}"
                >
                    <img
                        height="25"
                        width="25"
                        :src="'images/placeholder.jpg'"
                        alt=""
                        class="rounded-circle mr-1 hidden-image"
                    />
                    <div class="font-weight-bold mr-1 flex-grow-1 mobile-container-text">{{ item.name }}</div>
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
                <div v-for="item in messages">
                    <div
                        :class="[item.sender.id === $store.state.user.id ? 'justify-content-end': 'justify-content-start', 'd-flex mb-2']"
                        style="font-size: 12px">
                        <div style="width: 90%"
                             :class="[item.sender.id === $store.state.user.id ? 'bg-secondary': 'bg-primary', 'text-light with-shadow rounded-sm p-2']">
                            <div class="d-flex mb-1 mobile-container">
                                <div class="flex-grow-1 font-weight-bold d-flex align-items-center">
                                    <div v-show="item.sender.id !== $store.state.user.id && !item.read"
                                         class="bg-danger rounded-circle mr-1" style="width: 10px; height: 10px"/>
                                    {{ item.sender.name}}
                                </div>
                                <div class="font-weight-bold">
                                    12/12/2020 15:30
                                </div>
                            </div>
                            <div>
                                {{ item.message }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-primary">
                        Enviar Mensagem
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-reply-fill" viewBox="0 0 16 16">
                            <path
                                d="M9.079 11.9l4.568-3.281a.719.719 0 0 0 0-1.238L9.079 4.1A.716.716 0 0 0 8 4.719V6c-1.5 0-6 0-7 8 2.5-4.5 7-4 7-4v1.281c0 .56.606.898 1.079.62z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
/*jshint esversion: 6 */

export default {
    data() {
        return {
            contacts: [],
            messagesHistory: [],
            messages: [],
            response: null,
        };
    },
    methods: {
        setSelectedMessages(id) {
            this.messages = this.messagesHistory[id];
        },
    },
    mounted() {
        this.contacts = [{
            name: 'Marina Mota',
            photoUrl: 'xpto.jpg',
            id: 1,
        }, {
            name: 'Marina Motai',
            photoUrl: 'xpto.jpg',
            id: 2,
        },];

        this.messagesHistory = {
            1: [
                {
                    id: 1,
                    sender: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3,
                    },
                    message: 'Boa tarde! Tenho uma duvida relativa a xpto e quero saber xpta e preciso de xpti para isto e aquilo e esta mensagem é muito grande e não quero saber tem de ser cortada.',
                    receiver: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1
                    },
                    refMessageId: null,
                    read: true
                },
                {
                    id: 2,
                    sender: {
                        name: 'Profissional 1',
                        id: 1,
                    },
                    message: 'Boa tarde! Tem de fazer coisas a maneira',
                    receiver: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3
                    },
                    refMessageId: 1,
                    read: true
                },
                {
                    id: 3,
                    sender: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3,
                    },
                    message: 'Ok obrogado. Ainda preciso de saber se devo continuar a coimer kiwis',
                    receiver: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1
                    },
                    refMessageId: 2,
                    read: true
                },
                {
                    id: 4,
                    sender: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1,
                    },
                    message: 'Sim menos ao fim de semana',
                    receiver: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3
                    },
                    refMessageId: 3,
                    read: true
                },
                {
                    id: 5,
                    sender: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3,
                    },
                    message: 'E batatas posso comer a sexta?',
                    receiver: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1
                    },
                    refMessageId: 4,
                    read: true
                },
                {
                    id: 6,
                    sender: {
                        name: 'Profissional 1',
                        id: 1,
                    },
                    message: 'Sim mas apenas ao almoco',
                    receiver: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3
                    },
                    refMessageId: 5,
                    read: true
                },
                {
                    id: 7,
                    sender: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1,
                    },
                    message: 'Ok obrigado fico feliz com a resposta',
                    receiver: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 3
                    },
                    refMessageId: 5,
                    read: false
                }
            ],
            2: [
                {
                    id: 8,
                    sender: {
                        name: 'Marina Motai',
                        photoUrl: 'xpto.jpg',
                        id: 2,
                    },
                    message: 'Boa tarde! Tenho uma duvida relativa a xpto e quero saber xpta e preciso de xpti para isto e aquilo e esta mensagem é muito grande e não quero saber tem de ser cortada.',
                    receiver: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1
                    },
                    refMessageId: null,
                    read: true
                },
                {
                    id: 9,
                    sender: {
                        name: 'Marina Motai',
                        photoUrl: 'xpto.jpg',
                        id: 2,
                    },
                    message: 'E esquecime de dizer que o adoro',
                    receiver: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1
                    },
                    refMessageId: null,
                    read: true
                },
                {
                    id: 10,
                    sender: {
                        name: 'Profissional 1',
                        photoUrl: 'xpto.jpg',
                        id: 1,
                    },
                    message: 'Penso que a segunda parte nao seja adequada',
                    receiver: {
                        name: 'Marina Motai',
                        photoUrl: 'xpto.jpg',
                        id: 2
                    },
                    refMessageId: 9,
                    read: true
                },
                {
                    id: 11,
                    sender: {
                        name: 'Marina Mota',
                        photoUrl: 'xpto.jpg',
                        id: 2,
                    },
                    message: 'Porque?',
                    receiver: {
                        name: 'Profissional 1',
                        id: 1
                    },
                    refMessageId: 10,
                    read: true
                },
                {
                    id: 12,
                    sender: {
                        name: 'Profissional 1',
                        id: 1,
                    },
                    message: 'Maerina Motai sou o seu nutricionista e sou profissional',
                    receiver: {
                        name: 'Marina Motai',
                        id: 2
                    },
                    refMessageId: 11,
                    read: false
                },
                {
                    id: 13,
                    sender: {
                        name: 'Profissional 1',
                        id: 1,
                    },
                    message: 'Isto nao pode ser assim',
                    receiver: {
                        name: 'Marina Motai',
                        id: 2
                    },
                    refMessageId: 12,
                    read: false
                }, {
                    id: 14,
                    sender: {
                        name: 'Marina Motai',
                        id: 2,
                    },
                    message: 'Que pena...',
                    receiver: {
                        name: 'Profissional 1',
                        id: 1
                    },
                    refMessageId: 13,
                    read: false
                },]
        };

        this.messages = this.messagesHistory[this.$route.params.id];
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
