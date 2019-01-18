import Vue from 'vue';
import GuestHideEmailId from '../components/emails/GuestHideEmailId';
import VueAnimateNumber from 'vue-animate-number'
Vue.use(VueAnimateNumber)

if(document.getElementById('guestHideMailApp'))
{
    const guestHideMailApp = new Vue({
        el: '#guestHideMailApp',
        components: {
            GuestHideEmailId,
        },
        methods: {
            formatter(num)
            {
                return num.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }
    });
}