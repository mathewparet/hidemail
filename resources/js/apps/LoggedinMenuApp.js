import Vue from 'vue';
import UserMenu from '../components/users/UserMenu.vue';

if(document.getElementById('loggedInMenuApp'))
{
    const loggedInMenuApp = new Vue({
        el: '#loggedInMenuApp',
        components: {
            UserMenu,
        }
    });
}