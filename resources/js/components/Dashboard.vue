<template>
    <div class="row" v-if="!this.loading">
        <div class="col-md mb-3">
            <div class="card text-white bg-primary">
                <div class="card-header">Total Users</div>
                <div class="card-body">
                    <h3><span class="fas fa-users text-warning">&nbsp;</span>
                        <animate-number
                          from="0" 
                          :to="this.stats.num_users" 
                          duration="1000" 
                          class="float-right"
                          easing="easeOutQuad"
                          :formatter="formatter"
                        >                                                
                        </animate-number>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md mb-3">
            <div class="card text-white bg-primary">
                <div class="card-header">Total Apps</div>
                <div class="card-body">
                    <h3><i class="fas fa-rocket text-warning">&nbsp;</i>
                        <animate-number
                          from="0" 
                          :to="this.stats.num_apps" 
                          duration="1000" 
                          class="float-right"
                          easing="easeOutQuad"
                          :formatter="formatter"
                        >                                                
                        </animate-number>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md mb-3">
            <div class="card text-white bg-primary">
                <div class="card-header">Total API Keys</div>
                <div class="card-body">
                    <h3><span class="fas fa-fingerprint text-warning">&nbsp;</span> 
                        <animate-number
                          from="0" 
                          :to="this.stats.num_api_keys" 
                          duration="1000" 
                          easing="easeOutQuad"
                          :formatter="formatter"
                          class="float-right"
                        >                                                
                        </animate-number>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md mb-3">
            <div class="card text-white bg-primary">
                <div class="card-header">Total Emails</div>
                <div class="card-body">
                    <h3><span class="fas fa-at text-warning">&nbsp;</span> 
                        <animate-number
                          from="0" 
                          :to="this.stats.num_emails" 
                          duration="1000" 
                          class="float-right"
                          easing="easeOutQuad"
                          :formatter="formatter"
                        >                                                
                        </animate-number>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import VueAnimateNumber from 'vue-animate-number'
    Vue.use(VueAnimateNumber)
    export default {
        data()
        {
            return {
                stats: {},
                loading: true,
            }
        },
        mounted()
        {
            this.getStats();
        },
        methods: {
            getStats()
            {
                axios.get('/api/dashboard')
                    .then(response => this.stats = response.data)
                    .catch(error => {
                        this.$awn.alert(error.response.message);
                        this.$router.push({name: 'emails.index'})
                    })
                    .finally(() => this.loading = false);
            },
            formatter(num)
            {
                return num.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }
    }
</script>
