<template>
    <div class="top-bar">
        <div class="top-bar-container">
            <img :src="'images/logo_text_horizontal.png'" alt="" height="30px"/>
            <div style="display: flex">
                <div class="top-bar-info">
                    <div class="top-bar-avatar" v-if="!this.$store.state.user || !this.$store.state.user.avatarUrl">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
                            <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                        </svg>
                    </div>
                    <div v-else>
                        <img class="top-bar-avatar" :src="`http://nutriclock.test:81/storage/avatars/${this.$store.state.user.avatarUrl}`" alt="" />
                    </div>
                    <span class="top-bar-text">Bem vindo, {{this.$store.state.user ? this.$store.state.user.name : ''}}</span>
                </div>
                <div class="top-bar-items">
                    <div class="top-bar-menu-item" v-on:click="logout" data-toggle="tooltip" title="Terminar Sessão">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" fill="#A3DC92"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import { ROUTE } from '../../utils/routes';
    import Profile from '../modals/Profile';

    export default{
        data() {
            return {};
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
        },
        components: {
            Profile,
        }
    };
</script>

<style>
    .top-bar-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .top-bar {
        background: white;
        min-height: 45px;
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
        z-index: 2;
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

    .top-bar-text {
        font-family: 'Nunito';
        font-weight: bolder;
        margin-right: 16px;
    }

    .top-bar-items {
        display: flex;
    }

    .top-bar-menu-item {
        margin-left: 8px;
        cursor: pointer;
    }

    @media only screen and (max-width: 600px) {
        .top-bar-text {
            display: none;
        }
    }
</style>
