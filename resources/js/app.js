
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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
import Vuex from 'vuex';
Vue.use(Vuex);
Vue.prototype.$store = new Vuex.Store({
    state: {
        user: {

        }
    },
    mutations: {
        setUser(state, payload)
        {
            state.user = payload;
        },
        clearUser(state)
        {
            state.user = {};
        },
    },
    getters: {
        user: state => state.user,
    }
});

import Sidebar from './components/Sidebar.vue';
import {FacebookLoader, BulletListLoader, ContentLoader} from 'vue-content-loader';
import Axios from "axios";
Vue.component('bullet-list-loader', BulletListLoader);
Vue.component('facebook-loader', FacebookLoader);
Vue.component('content-loader', ContentLoader);

Axios.get('/api/user/info')
    .then(response => {
        (new Vue).$store.commit('setUser', response.data);
        initializeVue();
    })
    .catch(error => initializeVue());

function initializeVue()
{
    if(document.getElementById('app')) {
        window.app = new Vue({
            el: '#app',
            router: new Router({
                routes,
                linkActiveClass: 'active',
            }),
            components: {
                Sidebar,
                ContentLoader,
            }
        });   
    }

    require('./apps/RecaptchaApp');
    require('./apps/RevealEmailApp');
    require('./apps/GuestHideMailApp');
    require('./apps/LoggedinMenuApp');

}
