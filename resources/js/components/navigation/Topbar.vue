<template>
    <div class="top-bar">
        <div class="top-bar-container">
            <div style="display: flex">
                <div class="top-bar-items">
                    <button type="button" class="btn btn-outline-primary btn-sm mr-2
" v-on:click="logout">Logout</button>
                </div>
            </div>
            <div class="top-bar-info">
                <div class="top-bar-avatar" v-if="!this.$store.state.user || !this.$store.state.user.avatarUrl">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
                        <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                    </svg>
                </div>
                <div v-else>
                    <img class="top-bar-avatar" :src="`https://nutriclock.s3-eu-west-1.amazonaws.com/avatars/${this.$store.state.user.avatarUrl}`" alt="" />
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

    @media only screen and (max-width: 900px) {
        .top-bar {
            margin-left:46px;
        }
    }

</style>
