let routes = [
    {
        path: '*', 
        redirect: { name: 'emails.index' },
    },

    // start editing from here   
    {
        path: '/emails',
        component: require('./components/emails/index').default,
        name: 'emails.index',
        // beforeEnter(to, from, next) {
        //     authorize(next);
        // },
    },
    {
        path: '/apps',
        component: require('./components/passport/Clients.vue').default,
        name: 'passport.apps',
    },
    {
        path: '/authorized-apps',
        component: require('./components/passport/AuthorizedClients.vue').default,
        name: 'passport.authorized-apps',
    },
    {
        path: '/my-tokens',
        component: require('./components/passport/PersonalAccessTokens.vue').default,
        name: 'passport.tokens'
    }
    
    // do not edit beyond this line
];

export default routes;

function authorize(next, ability=null, type=null, param = null)
{
    if((new Vue()).$store.getters.isAuthenticated)
    {
        if(ability && type)
            (new Vue()).$gate.allow(ability,type, param) ? next() : next({name: 'dashboard'});
        else
            next();
    }
    else
    {
        window.location.reload();
    }
}