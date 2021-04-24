window.Vue = require('vue');

import Login from '../components/authentication/Login';
import ForgotPassword from '../components/authentication/ForgotPassword';
import ResetPassword from '../components/authentication/ResetPassword';
import AdminUsersList from '../components/users/AdminUsersList';
import CategoriesList from '../components/categories/CategoriesList';
import UsfsList from '../components/usfs/UsfsList';
import UserProfile from '../components/users/UserProfile';
import PatientsList from '../components/patients/PatientsList';
import AcceptanceTerms from '../components/utils/AcceptanceTerms';
import DiseasesList from '../components/diseases/DiseasesList';
import PatientTabs from '../components/patients/PatientTabs';
import SleepTipsList from '../components/tips/SleepTipsList';
import ConfigurationList from '../components/configurations/ConfigurationList';
import MessagesHistory from '../components/messages/MessagesHistory';
import ExercisesList from '../components/exercises/ExercisesList';
import FoodList from '../components/food/FoodList';

Vue.component('Login', Login);

Vue.component('ForgotPassword', ForgotPassword);

Vue.component('ResetPassword', ResetPassword);

Vue.component('AdminUsersList', AdminUsersList);

Vue.component('CategoriesList', CategoriesList);

Vue.component('UsfsList', UsfsList);

Vue.component('UserProfile', UserProfile);

Vue.component('PatientsList', PatientsList);

Vue.component('PatientTabs', PatientTabs);

Vue.component('AcceptanceTerms', AcceptanceTerms);

Vue.component('DiseasesList', DiseasesList);

Vue.component('SleepTipsList', SleepTipsList);

Vue.component('ConfigurationList', ConfigurationList);

Vue.component('MessagesHistory', MessagesHistory);

Vue.component('FoodList', FoodList);

Vue.component('ExercisesList', ExercisesList);

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
    SleepTips: '/sleep-tips',
    Diseases: '/diseases',
    Configurations: '/configurations',
    Messages: '/messages',
    Food: '/food',
    Exercises: '/exercises'
};

export const routes = [
    {path: '/', redirect: '/patients', name: 'root'},
    {path: '/login', component: Login, name: 'Login'},
    {path: '/forgot-password', component: ForgotPassword, name: 'ForgotPassword'},
    {path: '/reset-password/:token', component: ResetPassword, name: 'ResetPassword', props: true},
    {path: '/categories', component: CategoriesList, name: 'CategoriesList'},
    {path: '/usfs', component: UsfsList, name: 'UsfsList'},
    {path: '/admin/users', component: AdminUsersList, name: 'AdminUsersList'},
    {path: '/profile', component: UserProfile, name: 'UserProfile'},
    {path: '/patients', component: PatientsList, name: 'PatientsList'},
    {path: '/patients/:id/:nutriclockGroup', component: PatientTabs, name: 'PatientTabs', props: true},
    {path: '/terms', component: AcceptanceTerms, name: 'AcceptanceTerms'},
    {path: '/sleep-tips', component: SleepTipsList, name: 'SleepTipsList'},
    {path: '/diseases', component: DiseasesList, name: 'DiseasesList'},
    {path: '/configurations', component: ConfigurationList, name: 'ConfigurationList'},
    {path: '/messages/:id', component: MessagesHistory, name: 'MessagesHistory', props: true},
    {path: '/food', component: FoodList, name: 'FoodList'},
    {path: '/exercises', component: ExercisesList, name: 'ExercisesList'},
];
