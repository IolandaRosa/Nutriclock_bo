/*jshint esversion: 6 */
require('./bootstrap');
window.Vue = require('vue');

import { routes } from './utils/routes';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Toasted from 'vue-toasted';
Vue.use(Toasted);

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

// set language to EN
import lang from 'element-ui/lib/locale/lang/pt';
import locale from 'element-ui/lib/locale';

locale.use(lang);

import VueDataTables from 'vue-data-tables';
Vue.use(VueDataTables);

import Topbar from './components/navigation/Topbar';
import Sidebar from './components/navigation/Sidebar';

import store from './stores/global-store';

const router = new VueRouter({
    routes,
});

router.beforeEach((to, from, next) => {
    next();
});

const app = new Vue({
    router,
    store,
    components: {
        Topbar,
        Sidebar,
    },
    created() {
        this.$store.commit('loadTokenAndUserFromSession');
    },
}).$mount('#app');