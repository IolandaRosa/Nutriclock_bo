/*jshint esversion: 6 */
import {parseSocketMessage} from "./utils/misc";

require('./bootstrap');
require('datatables.net-responsive-bs4');
window.Vue = require('vue');

import { routes } from './utils/routes';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Toasted from 'vue-toasted';
Vue.use(Toasted);

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

// set language to PT
import lang from 'element-ui/lib/locale/lang/pt';
import locale from 'element-ui/lib/locale';

locale.use(lang);

import VueDataTables from 'vue-data-tables';
Vue.use(VueDataTables);

import VueNativeSock from 'vue-native-websocket';
Vue.use(VueNativeSock, 'wss://nutriclock-websocket.herokuapp.com', {
    reconnection: true
});

import '@pencilpix/vue2-clock-picker/dist/vue2-clock-picker.min.css';
const VueClockPickerPlugin = require('@pencilpix/vue2-clock-picker/dist/vue2-clock-picker.plugin.js')
Vue.use(VueClockPickerPlugin);

import Topbar from './components/navigation/Topbar';
import Sidebar from './components/navigation/Sidebar';
import RequestForget from './components/navigation/RequestForget';

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
        RequestForget,
    },
    created() {
        this.$store.commit('loadTokenAndUserFromSession');
    },
}).$mount('#app');
