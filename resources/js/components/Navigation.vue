<template>
    <span>
        <div class="row">
            <div class="col">
                <div class="card mb-3">
                    <div class="navbar navbar-expand-lg navbar-light navbar-laravel pl-0 pr-0">
                        <div class="container pl-0 pr-0">
                            <div class="card-heading ml-2">
                                <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#sidebarNav" aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                            </div>
                            <div class="collapse navbar-collapse" id="sidebarNav">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-ellipsis-v"></i> Navigation</h5>
                                    <span class="card-text">
                                        <div class="list-group list-group-flush"> 
                                            <router-link :to="{name: 'emails.index'}" class="list-group-item list-group-item-action"><i class="fas fa-at"></i> Email IDs</router-link>
                                            <router-link :to="{name: 'passport.apps'}" class="list-group-item list-group-item-action"><i class="fas fa-rocket"></i> My Apps</router-link>
                                            <router-link :to="{name: 'passport.authorized-apps'}" class="list-group-item list-group-item-action"><i class="fas fa-key"></i> Authorized Apps</router-link>
                                            <router-link :to="{name: 'passport.tokens'}" class="list-group-item list-group-item-action"><i class="fas fa-fingerprint"></i> API Tokens</router-link>
                                            <router-link :to="{name: 'users.edit', params: {id: this.user.id}}" class="list-group-item list-group-item-action"><i class="fas fa-user-circle"></i> My Profile</router-link>
                                            <a href="https://documenter.getpostman.com/view/5815935/RznJoGx5" target="__blank" class="list-group-item list-group-item-action"><i class="fas fa-atom"></i> API Docs</a>
                                            <router-link v-if="this.user.id === 1" :to="{name: 'users.index'}" class="list-group-item list-group-item-action"><i class="fas fa-users"></i> Users</router-link>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
    import {mapState} from 'vuex';

    export default {
        computed: {
            ...mapState(['user']),
        },
    }
</script>
