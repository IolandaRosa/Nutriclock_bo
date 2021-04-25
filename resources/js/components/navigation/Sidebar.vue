<template>
    <div class="sidebar overflow-auto">
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
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="usfsRoute" data-toggle="tooltip" title="Instituições">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 3.293l6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6zm5-.793V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
            </svg>
            <div class="sidebar-item-text">Instituições</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="diseasesRoute" data-toggle="tooltip" title="Patologias">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-heart-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
            </svg>
            <div class="sidebar-item-text">Patologias</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="foodRoute" data-toggle="tooltip" title="Alimentos">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cup-straw" viewBox="0 0 16 16">
                <path d="M13.902.334a.5.5 0 0 1-.28.65l-2.254.902-.4 1.927c.376.095.715.215.972.367.228.135.56.396.56.82 0 .046-.004.09-.011.132l-.962 9.068a1.28 1.28 0 0 1-.524.93c-.488.34-1.494.87-3.01.87-1.516 0-2.522-.53-3.01-.87a1.28 1.28 0 0 1-.524-.93L3.51 5.132A.78.78 0 0 1 3.5 5c0-.424.332-.685.56-.82.262-.154.607-.276.99-.372C5.824 3.614 6.867 3.5 8 3.5c.712 0 1.389.045 1.985.127l.464-2.215a.5.5 0 0 1 .303-.356l2.5-1a.5.5 0 0 1 .65.278zM9.768 4.607A13.991 13.991 0 0 0 8 4.5c-1.076 0-2.033.11-2.707.278A3.284 3.284 0 0 0 4.645 5c.146.073.362.15.648.222C5.967 5.39 6.924 5.5 8 5.5c.571 0 1.109-.03 1.588-.085l.18-.808zm.292 1.756C9.445 6.45 8.742 6.5 8 6.5c-1.133 0-2.176-.114-2.95-.308a5.514 5.514 0 0 1-.435-.127l.838 8.03c.013.121.06.186.102.215.357.249 1.168.69 2.438.69 1.27 0 2.081-.441 2.438-.69.042-.029.09-.094.102-.215l.852-8.03a5.517 5.517 0 0 1-.435.127 8.88 8.88 0 0 1-.89.17zM4.467 4.884s.003.002.005.006l-.005-.006zm7.066 0l-.005.006c.002-.004.005-.006.005-.006zM11.354 5a3.174 3.174 0 0 0-.604-.21l-.099.445.055-.013c.286-.072.502-.149.648-.222z"/>
            </svg>
            <div class="sidebar-item-text">Alimentos</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="exercisesRoute" data-toggle="tooltip" title="Exercícios">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-alt" viewBox="0 0 16 16">
                <path d="M7 2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm4.225 4.053a.5.5 0 0 0-.577.093l-3.71 4.71-2.66-2.772a.5.5 0 0 0-.63.062L.002 13v2a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4.5l-4.777-3.947z"/>
            </svg>
            <div class="sidebar-item-text">Exercícios</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role !== 'PATIENT'" class="menu-item" active-class="active" :to="sleepTipsRoute" data-toggle="tooltip" title="Dicas de Sono">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-patch-check-fll" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984a.5.5 0 0 0-.708-.708L7 8.793 5.854 7.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
            </svg>
            <div class="sidebar-item-text">Dicas de Sono</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="biometricIndicators" data-toggle="tooltip" title="Indicadores Biométricos">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eyedropper" viewBox="0 0 16 16">
                <path d="M13.354.646a1.207 1.207 0 0 0-1.708 0L8.5 3.793l-.646-.647a.5.5 0 1 0-.708.708L8.293 5l-7.147 7.146A.5.5 0 0 0 1 12.5v1.793l-.854.853a.5.5 0 1 0 .708.707L1.707 15H3.5a.5.5 0 0 0 .354-.146L11 7.707l1.146 1.147a.5.5 0 0 0 .708-.708l-.647-.646 3.147-3.146a1.207 1.207 0 0 0 0-1.708l-2-2zM2 12.707l7-7L10.293 7l-7 7H2v-1.293z"/>
            </svg>
            <div class="sidebar-item-text">Indicadores Biométricos</div>
        </router-link>
        <router-link v-show="this.$store.state.user && this.$store.state.user.role === 'ADMIN'" class="menu-item" active-class="active" :to="acceptanceTermsRoute" data-toggle="tooltip" title="Termos de Aceitação">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-list-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
            </svg>
            <div class="sidebar-item-text">Termos de Aceitação</div>
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
                configurationsRoute: ROUTE.Configurations,
                foodRoute: ROUTE.Food,
                exercisesRoute: ROUTE.Exercises,
                biometricIndicators: ROUTE.BiometricIndicators
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
