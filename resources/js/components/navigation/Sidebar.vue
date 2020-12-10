<template>
    <div class="sidebar">
        <div class="sidebar-image-container">
            <img :src="'images/logo_text_horizontal.png'" alt="" height="40px"/>
        </div>
        <div class="sidebar-image-small-container">
            <img :src="'images/only_logo.png'" alt="" height="30px"/>
        </div>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="adminUsersRoute" data-toggle="tooltip" title="Utilizadores">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-lines-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm7 1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm2 9a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
            </svg>
            <div class="sidebar-item-text">Utilizadores BO</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role !== 'PATIENT'" class="menu-item" active-class="active" :to="patientsRoute" data-toggle="tooltip" title="Utentes">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-people-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
            </svg>
            <div class="sidebar-item-text">Utentes</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="categoriesRoute" data-toggle="tooltip" title="Categorias Profissionais">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-tags-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 7.586 1H3zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                <path d="M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z"/>
            </svg>
            <div class="sidebar-item-text">Categorias Profissionais</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="usfsRoute" data-toggle="tooltip" title="USF">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 3.293l6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6zm5-.793V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
            </svg>
            <div class="sidebar-item-text">USF</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="diseasesRoute" data-toggle="tooltip" title="Patologias">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-heart-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
            </svg>
            <div class="sidebar-item-text">Patologias</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role !== 'PATIENT'" class="menu-item" active-class="active" :to="sleepTipsRoute" data-toggle="tooltip" title="Dicas de Sono">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-patch-check-fll" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984a.5.5 0 0 0-.708-.708L7 8.793 5.854 7.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
            </svg>
            <div class="sidebar-item-text">Dicas de Sono</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="acceptanceTermsRoute" data-toggle="tooltip" title="Termos de Aceitação">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-list-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
            </svg>
            <div class="sidebar-item-text">Termos de Aceitação</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="configurationsRoute" data-toggle="tooltip" title="Configurações">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 0 0-5.86 2.929 2.929 0 0 0 0 5.858z"/>
            </svg>
            <div class="sidebar-item-text">Configurações</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role !== 'PATIENT'" class="menu-item" active-class="active" :to="profileRoute" data-toggle="tooltip" title="Perfil">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-bounding-box" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"/>
                <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
            <div class="sidebar-item-text">Perfil</div>
        </router-link>
    </div>
</template>

<script type="text/javascript">
    /*jshint esversion: 6 */
    import { ROUTE } from '../../utils/routes';

    export default{
        data() {
            return {
                categoriesRoute: ROUTE.Categories,
                usfsRoute: ROUTE.Usfs,
                adminUsersRoute: ROUTE.AdminUsers,
                profileRoute: ROUTE.Profile,
                patientsRoute: ROUTE.Patients,
                acceptanceTermsRoute: ROUTE.AcceptanceTerms,
                sleepTipsRoute: ROUTE.SleepTips,
                diseasesRoute: ROUTE.Diseases,
                configurationsRoute: ROUTE.Configurations
            };
        },
    };
</script>

<style>
    .sidebar {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        position: fixed;
        top: 0;
        bottom: 0;
        background: white;
        width: 200px;
        box-shadow: 0 3px 6px #0f0f0f28;
        z-index: 1;
        flex-direction: column;
        padding-top: 60px;
    }

    .sidebar-image-container {
        width: 100%;
        padding: 8px 0 60px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    a.menu-item {
        color: #a3dc92;
        font-weight: 700;
        align-items: center;
        justify-content: flex-start;
        padding: 16px;
        font-size: 14px;
        width: 100%;
        text-decoration: none;
    }

    .sidebar-item-text {
        margin-left: 16px;
    }

    a.menu-item:hover {
        color: white;
        background: #bce6b0;
        text-decoration: none;
        opacity:0.7;
    }

    a.active {
        color: white !important;
        background: #a3dc92 !important;
        box-shadow: 0 3px 6px #0f0f0f28;
    }

    .sidebar-image-small-container {
        display:none;
    }

    @media only screen and (max-width: 900px) {
        .sidebar {
            width: unset;
        }

        .sidebar-item-text {
            display: none;
        }

        a {
            justify-content: center;
        }

        .sidebar-image-container {
            display: none;
        }

        .sidebar-image-small-container {
            width: 100%;
            padding: 8px 0 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }
</style>
