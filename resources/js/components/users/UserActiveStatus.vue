<template>
    <span>
        <a class="badge action-link text-white" v-bind:class="{'badge-danger': this.personel.suspended, 'badge-success': !this.personel.suspended}" @click.prevent="toggleSuspension">
            <span v-if="this.personel.suspended === 1">Suspended</span>
            <span v-else>Active</span>
        </a>
    </span>
</template>

<script>
    import { mapState } from 'vuex';

    export default {
        props: ['value','person'],
        computed: {
            ...mapState(['user']),
            personel()
            {
                return this.value.suspended == null ? this.person : this.value;
            }
        },
        methods: {
            toggleSuspension: function() {
                axios
                  .post('/api/users/'+this.personel.id+'/toggle-suspension')
                  .then(response => {
                    if(response.status == 200)
                    { 
                        this.$emit('input',this.value.suspended == null ? response.data.user.suspended : response.data.user);

                        if(response.data.user.suspended)
                            status = 'Suspended';
                        else
                            status = 'Active';

                        this.$awn.success(this.personel.email+' is now '+ status);
                    }
                  })
                  .catch(error => {
                    this.$awn.alert('Error toggling suspension status for '+this.personel.email+'. '+error.response.data.message);
                  });
            }
        }
    }
</script>
