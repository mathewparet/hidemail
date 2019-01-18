<template>
    <div class="container">
        <navigation></navigation>
        <admin-menu v-if="this.user.id===1"></admin-menu>
    </div>
</template>
<script>
    import Navigation from './Navigation';
    import AdminMenu from './AdminMenu';
    import {mapState} from 'vuex';
    export default {
       components: {
           AdminMenu,
           Navigation,
       },
       computed: {
           ...mapState(['user']),
       },
    }
</script>
