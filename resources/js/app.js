
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Recaptcha from '@mathewparet/vue-recaptcha-invisible';
Vue.use(Recaptcha);

import VueAWN from "vue-awesome-notifications"
Vue.use(VueAWN);

import FilterBox from '@mathewparet/vue-filter-box';
Vue.use(FilterBox);

import filters from '@mathewparet/vue-common-filters';
Vue.use(filters);

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);

import Router from 'vue-router';
Vue.use(Router);

import routes from './routes.def.js'

import Sidebar from './components/Sidebar.vue';
import {FacebookLoader, BulletListLoader} from 'vue-content-loader';
Vue.component('bullet-list-loader', BulletListLoader);
Vue.component('facebook-loader', FacebookLoader);

const app = new Vue({
    el: '#app',
    router: new Router({
        routes,
        linkActiveClass: 'active',
    }),
    components: {
        Sidebar,
    }
});
