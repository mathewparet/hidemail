<template>
    <div class="card card-default">
        <div class="card-header">
            <span>
                <i class="fas fa-users"></i> Registered Users
            </span>
            <span class="float-right">
                <filter-box placeholder="Min 3 chars or full email" class="form-control form-control-sm" @filter="loadUsers" v-model="filterString" title="Since email IDs are encrypted in the database, searching / filtering of email IDs by partial match doesn't work. You will need to enter the complete email ID you are looking for in order to filter it. The search is, however, case-insensitive." v-b-tooltip/>
            </span>
        </div>
        <div class="card-body">
            <div class="card-text">
                <bullet-list-loader :step=2 :animate="true" v-if="this.loading === true"/>
                <b-table stacked="md" v-else-if="this.users.data && this.users.data.length > 0" hover :items="this.users.data" :fields="this.fields">
                    <template slot="name" slot-scope="data">
                        <span :title="data.item.name" v-b-tooltip>{{data.item.name | trim(20)}}</span>
                    </template>
                    <template slot="email" slot-scope="data">
                        <span :title="data.item.email" v-b-tooltip>{{data.item.email | trim(20)}}</span>
                        <b-badge variant="warning" v-if="data.item.verified">Unverified</b-badge>
                        <b-badge variant="success" v-else>Verified</b-badge>
                    </template>
                    <template slot="created_at" slot-scope="data">
                        {{data.item.created_at}}
                    </template>
                    <template slot="status" slot-scope="data">
                        <user-active-status v-model="data.item.suspended" :person="data.item"/> 
                    </template>
                    <template slot="action" slot-scope="data">
                        <b-button-group size="sm">
                            <router-link class="btn btn-sm btn-secondary" :to="{name: 'users.edit', params: {id: data.item.id}}"><i class="fas fa-user-edit"></i></router-link>
                        </b-button-group>
                    </template>
                </b-table>
                <div v-else>
                    <div class="text-center mb-2">
                        You have not hidden any email IDs <span v-if="filterString.length > 0">that matches <em class="text-info">{{filterString}}</em> <a title="Remove filter" v-b-tooltip class="action-link card-link" @click="()=>{this.filterString = '';}"><i class="fas fa-times-circle"></i></a></span>.
                    </div>
                    <b-alert show v-if="filterString.length > 0">
                        If you are searching by partial name, it is fine. But since email IDs are encrypted in the database, searching / filtering of email IDs by partial match doesn't work. You will need to enter the complete email ID you are looking for in order to filter it. The search is, however, case-insensitive.
                    </b-alert>
                </div>
                <div class="text-center">
                    <pagination :data="users" @pagination-change-page="loadUsers" :limit=2></pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import FilterBox from '@mathewparet/vue-filter-box';
    import Pagination from 'laravel-vue-pagination';
    import UserActiveStatus from './UserActiveStatus';
    export default {
        components: {
            FilterBox,
            Pagination,
            UserActiveStatus,
        },
        data()
        {
            return {
                users: {},
                loading: true,
                filterString: '',
                fields: ['id', 'name', 'email', 'status', 'created_at', 'action']
            }
        },
        methods: {
            loadUsers(page = 1)
            {
                this.loading = true;
                let filter = this.filterString == null ? '' : this.filterString;
                axios.get(`/api/users?page=${page}&filter=${filter}`)
                    .then(response => {
                        this.users = response.data.users;
                    })
                    .catch(error => {
                        if(error.response.status == 403)
                        {
                            this.$awn.alert('You are not authorized to access this page.');
                            this.$router.push({name: 'emails.index'});
                        }
                        else
                            this.$awn.alert(error.message);
                    })
                    .finally(() => this.loading = false);
            }
        }
    }
</script>
