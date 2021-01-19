/*jshint esversion: 6 */
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import VuexPersistence from 'vuex-persist';

const vuexLocal = new VuexPersistence({
    storage: window.localStorage
});

export default new Vuex.Store({
    state: {
        token: "",
        user: null,
        userUfcs: [],
        unread: 0,
    },
    mutations: {
        clearUserAndToken: (state) => {
            state.user = null;
            state.token = "";
            state.unread = 0;
            state.userUfcs = [];
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            localStorage.removeItem('unread');
            localStorage.removeItem('userUfcs');
            axios.defaults.headers.common.Authorization = undefined;
        },
        clearUser: (state) => {
            state.user = null;
            localStorage.removeItem('user');
        },
        clearToken: (state) => {
            state.token = "";
            localStorage.removeItem('token');
            axios.defaults.headers.common.Authorization = undefined;
        },
        setUserUfcs: (state, ufcs) => {
            state.userUfcs = ufcs;
            localStorage.setItem('userUfcs', JSON.stringify(ufcs));
        },
        setUser: (state, user) => {
            state.user =  user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        setToken: (state, token) => {
            state.token= token;
            localStorage.setItem('token', token);
            axios.defaults.headers.common.Authorization = "Bearer " + token;
        },
        loadTokenAndUserFromSession: (state) => {
            state.token = "";
            state.user = null;
            let token = localStorage.getItem('token');
            let user = localStorage.getItem('user');
            if (token) {
                state.token = token;
                axios.defaults.headers.common.Authorization = "Bearer " + token;
            }
            if (user) {
                state.user = JSON.parse(user);
            }
        },
        setUnread: (state, unread) => {
            state.unread =  unread;
            localStorage.setItem('unread', unread);
        }
    },
    plugins: [vuexLocal.plugin],
});
