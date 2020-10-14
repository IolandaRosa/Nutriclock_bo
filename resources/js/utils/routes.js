window.Vue = require('vue');
// Authentication
import Login from '../components/authentication/Login';
Vue.component('Login', Login);

import ForgotPassword from '../components/authentication/ForgotPassword';
Vue.component('ForgotPassword', ForgotPassword);

import ResetPassword from '../components/authentication/ResetPassword';
Vue.component('ResetPassword', ResetPassword);

// Users
import AdminUsersList from '../components/users/AdminUsersList';
Vue.component('AdminUsersList', AdminUsersList);

// Categories
import CategoriesList from '../components/categories/CategoriesList';
Vue.component('CategoriesList', CategoriesList);

// Usfs
import UsfsList from '../components/usfs/UsfsList';
Vue.component('UsfsList', UsfsList);

import UserProfile from '../components/users/UserProfile';
Vue.component('UserProfile', UserProfile);

import PatientsList from '../components/users/PatientsList';
Vue.component('PatientsList', PatientsList);

import Patient from '../components/users/Patient';
Vue.component('Patient', Patient);

import AcceptanceTerms from '../components/utils/AcceptanceTerms';
Vue.component('AcceptanceTerms', AcceptanceTerms);

export const ROUTE = {
    Index: '/',
    Login: '/login',
    ForgotPassword: '/forgot-password',
    ResetPassword: '/reset-password',
    Categories: '/categories',
    Usfs: '/usfs',
    AdminUsers: '/admin/users',
    Profile: '/profile',
    Patients: '/patients',
    AcceptanceTerms: '/terms',
};

export const routes = [
    { path: '/', redirect: '/login', name: 'root' },
    { path: '/login', component: Login, name: 'Login'},
    { path: '/forgot-password', component: ForgotPassword, name: 'ForgotPassword'},
    { path: '/reset-password/:token', component: ResetPassword, name: 'ResetPassword', props: true},
    { path: '/categories', component: CategoriesList, name: 'CategoriesList'},
    { path: '/usfs', component: UsfsList, name: 'UsfsList'},
    { path: '/admin/users', component: AdminUsersList, name: 'AdminUsersList'},
    { path: '/profile', component: UserProfile, name: 'UserProfile'},
    { path: '/patients', component: PatientsList, name: 'PatientsList'},
    { path: '/patients/:id', component: Patient, name: 'Patient', props: true},
    { path: '/terms', component: AcceptanceTerms, name: 'AcceptanceTerms'},
];
