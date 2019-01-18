<template>
    <collapsable-menu id="sidbarAdmin" title="Admin" icon="fas fa-lock">
        <router-link :to="{name: 'users.index'}" class="list-group-item list-group-item-action"><i class="fas fa-users"></i> Users</router-link>
    </collapsable-menu>
</template>
<script>
    import CollapsableMenu from './CollapsableMenu';

    export default {
        components: {
            CollapsableMenu,
        }
    }
</script>
