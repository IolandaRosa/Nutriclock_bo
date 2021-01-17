<template>
    <div class="top-bar">
        <div class="top-bar-container">
            <div style="display: flex">
                <div class="top-bar-items">
                    <button type="button" class="btn btn-outline-primary btn-sm mr-2" data-toggle="tooltip" title="Logout" v-on:click="logout">Logout</button>
                    <button
                        type="button" data-toggle="tooltip" title="Configurações"
                        v-if="$store.state.user && $store.state.user.role === 'ADMIN'"
                        class="btn btn-outline-primary btn-sm mr-2"
                        v-on:click="() => {this.$router.push('/configurations')}"
                    >
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-tools" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M0 1l1-1 3.081 2.2a1 1 0 0 1 .419.815v.07a1 1 0 0 0 .293.708L10.5 9.5l.914-.305a1 1 0 0 1 1.023.242l3.356 3.356a1 1 0 0 1 0 1.414l-1.586 1.586a1 1 0 0 1-1.414 0l-3.356-3.356a1 1 0 0 1-.242-1.023L9.5 10.5 3.793 4.793a1 1 0 0 0-.707-.293h-.071a1 1 0 0 1-.814-.419L0 1zm11.354 9.646a.5.5 0 0 0-.708.708l3 3a.5.5 0 0 0 .708-.708l-3-3z"/>
                            <path fill-rule="evenodd" d="M15.898 2.223a3.003 3.003 0 0 1-3.679 3.674L5.878 12.15a3 3 0 1 1-2.027-2.027l6.252-6.341A3 3 0 0 1 13.778.1l-2.142 2.142L12 4l1.757.364 2.141-2.141zm-13.37 9.019L3.001 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z"/>
                        </svg>
                    </button>
                    <button
                        v-if="$store.state.user && $store.state.user.role === 'PROFESSIONAL'"
                        type="button" data-toggle="tooltip" title="Mensagens"
                        class="btn btn-outline-primary btn-sm mr-2"
                        v-on:click="() => { this.showMessageSidebar = true; }"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text-fill" viewBox="0 0 16 16">
                            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
                        </svg>
                        <span v-show="$store.state.unread" class="badge badge-pill badge-danger">{{ $store.state.unread }}</span>
                    </button>
                </div>
            </div>
            <div class="top-bar-info pointer" data-toggle="tooltip" title="Perfil" v-on:click="() => {this.$router.push('/profile')}">
                <div class="top-bar-avatar" v-if="!this.$store.state.user || !this.$store.state.user.avatarUrl">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
                        <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                    </svg>
                </div>
                <div v-else>
                    <img
                        class="top-bar-avatar"
                        :src="`https://nutriclock.s3-eu-west-1.amazonaws.com/avatars/${this.$store.state.user.avatarUrl}`"
                        alt=""
                        @error="setAltImage"
                    />
                </div>
            </div>
        </div>
        <MessageSidebar
            v-show="showMessageSidebar"
            @close-sidebar-message="() => { this.showMessageSidebar = false }"
        />
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import { ROUTE } from '../../utils/routes';
    import Profile from '../modals/Profile';
    import MessageSidebar from '../messages/MessageSidebar';
    import { EventType } from '../../constants/misc';
    import { parseSocketMessage } from '../../utils/misc';

    export default{
        data() {
            return {
                showMessageSidebar: false,
            };
        },
        methods:{
            logout() {
                axios.post('/api/logout').then(() =>  {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                }).catch(() => {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push({path: ROUTE.Login });
                });
            },
            setAltImage(event) {
                event.target.src = '/images/avatar.jpg';
            }
        },
        components: {
            Profile,
            MessageSidebar,
        },
        mounted() {
            this.$options.sockets.onmessage = (data) => {
                if (data && data.data) {
                    const message = parseSocketMessage(data.data);

                    if (Number(message.receiverId) === this.$store.state.user.id && message) {
                        if (message.eventType === EventType.Store) {
                            const value = this.$store.state.unread + 1;
                            this.$store.commit('setUnread', value);
                        }

                        if (message.eventType === EventType.OneRead) {
                            const value = this.$store.state.unread - 1;
                            this.$store.commit('setUnread', value);
                        }
                    }
                }
            }
        },
        unmounted() {
            delete this.$options.sockets.onmessage;
        }
    };
</script>

<style>
    .top-bar-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
    }

    .top-bar {
        margin-left: 200px;
        background: white;
        min-height: 60px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        box-shadow: 0 3px 6px #0f0f0f28;
        color: #cceee1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 0 20px;
        z-index: 1;
    }

    .top-bar-info {
        display: flex;
        align-items: center;
        color: #7dce65;
    }

    .top-bar-avatar {
        height: 30px;
        width: 30px;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 0 3px 3px #c0c0c068;
        margin-right: 8px;
        display:flex;
        justify-content: center;
        align-items: center;
        color: #FFF;
        background: #a3dc92;
    }

    .top-bar-items {
        display: flex;
    }

    @media only screen and (max-width: 900px) {
        .top-bar {
            margin-left:46px;
        }
    }

</style>
