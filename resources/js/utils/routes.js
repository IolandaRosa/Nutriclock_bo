window.Vue = require('vue');

import Login from '../components/authentication/Login';
import ForgotPassword from '../components/authentication/ForgotPassword';
import ResetPassword from '../components/authentication/ResetPassword';
import AdminUsersList from '../components/users/AdminUsersList';
import CategoriesList from '../components/categories/CategoriesList';
// Usfs
import UsfsList from '../components/usfs/UsfsList';
import UserProfile from '../components/users/UserProfile';
import PatientsList from '../components/users/PatientsList';
import Patient from '../components/users/Patient';
import AcceptanceTerms from '../components/utils/AcceptanceTerms';
import DiseasesList from '../components/diseases/DiseasesList';

Vue.component('Login', Login);

Vue.component('ForgotPassword', ForgotPassword);

Vue.component('ResetPassword', ResetPassword);

Vue.component('AdminUsersList', AdminUsersList);

Vue.component('CategoriesList', CategoriesList);

Vue.component('UsfsList', UsfsList);

Vue.component('UserProfile', UserProfile);

Vue.component('PatientsList', PatientsList);

Vue.component('Patient', Patient);

Vue.component('AcceptanceTerms', AcceptanceTerms);

Vue.component('DiseasesList', DiseasesList);

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
    Diseases: '/diseases',
};

export const routes = [
    {path: '/', redirect: '/login', name: 'root'},
    {path: '/login', component: Login, name: 'Login'},
    {path: '/forgot-password', component: ForgotPassword, name: 'ForgotPassword'},
    {path: '/reset-password/:token', component: ResetPassword, name: 'ResetPassword', props: true},
    {path: '/categories', component: CategoriesList, name: 'CategoriesList'},
    {path: '/usfs', component: UsfsList, name: 'UsfsList'},
    {path: '/admin/users', component: AdminUsersList, name: 'AdminUsersList'},
    {path: '/profile', component: UserProfile, name: 'UserProfile'},
    {path: '/patients', component: PatientsList, name: 'PatientsList'},
    {path: '/patients/:id', component: Patient, name: 'Patient', props: true},
    {path: '/terms', component: AcceptanceTerms, name: 'AcceptanceTerms'},
    {path: '/diseases', component: DiseasesList, name: 'DiseasesList'},
];
