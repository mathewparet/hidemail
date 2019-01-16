import Vue from 'vue';
import VueRecaptcha from 'vue-recaptcha';

if(document.getElementById('recaptchaApp'))
{
    const recaptchaApp = new Vue({
        el: '#recaptchaApp',
        components: {
            VueRecaptcha,
        }
    });
}