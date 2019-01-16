import Vue from 'vue';
import VueRecaptcha from 'vue-recaptcha';
import EmailRevealForm from '../components/emails/EmailRevealForm';

if(document.getElementById('revealEmailApp'))
{
    const revealEmailApp = new Vue({
        el: '#revealEmailApp',
        components: {
            VueRecaptcha,
            EmailRevealForm,
        }
    });
}