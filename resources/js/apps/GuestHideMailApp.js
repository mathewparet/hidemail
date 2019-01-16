import Vue from 'vue';
import GuestHideEmailId from '../components/emails/GuestHideEmailId';

if(document.getElementById('guestHideMailApp'))
{
    const guestHideMailApp = new Vue({
        el: '#guestHideMailApp',
        components: {
            GuestHideEmailId,
        }
    });
}