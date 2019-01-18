<template>
    <collapsable-menu id="sidebarNav" title="Navigation" icon="fas fa-ellipsis-v">
        <router-link :to="{name: 'emails.index'}" class="list-group-item list-group-item-action"><i class="fas fa-at"></i> Email IDs</router-link>
        <router-link to="/profile" class="list-group-item list-group-item-action"><i class="fas fa-user-circle"></i> My Profile</router-link>
        <router-link :to="{name: 'passport.apps'}" class="list-group-item list-group-item-action"><i class="fas fa-rocket"></i> My Apps</router-link>
        <router-link :to="{name: 'passport.authorized-apps'}" class="list-group-item list-group-item-action"><i class="fas fa-key"></i> Authorized Apps</router-link>
        <router-link :to="{name: 'passport.tokens'}" class="list-group-item list-group-item-action"><i class="fas fa-fingerprint"></i> API Tokens</router-link>
        <a href="https://documenter.getpostman.com/view/5815935/RznJoGx5" target="__blank" class="list-group-item list-group-item-action"><i class="fas fa-atom"></i> API Docs</a>
    </collapsable-menu>
</template>
<script>
    import CollapsableMenu from './CollapsableMenu';
    export default {
        components: {
            CollapsableMenu
        }
    }
</script>
