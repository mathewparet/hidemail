<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <span>
                    <i class="fas fa-at"></i> My Hidden Email IDs
                </span>
                <span class="float-right">
                    <filter-box placeholder="Full email address" class="form-control form-control-sm" @filter="loadEmails" v-model="filterString" title="Since email IDs are encrypted in the database, searching / filtering of email IDs by partial match doesn't work. You will need to enter the complete email ID you are looking for in order to filter it. The search is, however, case-insensitive." v-b-tooltip/>
                </span>
            </div>
            <div class="card-body">
                <div class="card-text">
                    <form @submit.prevent="addEmail" @keydown="hideEmailForm.errors.clear($event.target.name)">                                
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="email" ref="emailField" class="form-control" :class="{'is-invalid': hideEmailForm.errors.has('email')}" v-model="hideEmailForm.email" name="email" :disabled="hideEmailForm.busy" placeholder="Email ID to be hidden. E.g. johndoe@example.com">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary btn-sm" @click.prevent="addEmail" :disabled=this.hideEmailForm.busy>Add Email</button>
                                    </div>
                                </div>
                                <span class="invalid-feedback" style="display: block;">
                                    <strong>{{ hideEmailForm.errors.get('email') }}</strong>
                                </span>
                            </div>
                        </div>
                    </form>
                    <bullet-list-loader :step=2 :animate="true" v-if="this.loading === true"/>
                    <b-table v-else-if="this.emails.data && this.emails.data.length > 0" hover :items="this.emails.data" :fields="this.fields" caption="Your hidden email Ids" caption-top>
                        <template slot="hidden_email" slot-scope="data">
                            <a :href="data.item.link" target="__blank" class="card-link">{{data.item.hidden_email}}</a>
                        </template>
                        <template slot="action" slot-scope="data">
                            <b-button-group size="sm">
                                <b-button size="sm" variant="secondary" @click.prevent="showLinkOptions(data.item)"><span class="fas fa-link"></span></b-button>
                                <b-button size="sm" variant="danger" @click.prevent="deleteEmail(data.item)"><span class="fas fa-trash text-white"></span></b-button>
                            </b-button-group>
                        </template>
                    </b-table>
                    <div v-else>
                        <div class="text-center mb-2">
                            You have not hidden any email IDs <span v-if="filterString.length > 0">that matches <em class="text-info">{{filterString}}</em> <a title="Remove filter" v-b-tooltip class="action-link card-link" @click="()=>{this.filterString = '';}"><i class="fas fa-times-circle"></i></a></span>.
                        </div>
                         <b-alert show v-if="filterString.length > 0">
                            Since email IDs are encrypted in the database, searching / filtering of email IDs by partial match doesn't work. You will need to enter the complete email ID you are looking for in order to filter it. The search is, however, case-insensitive.
                         </b-alert>
                    </div>
                    <div class="text-center">
                        <pagination :data="emails" @pagination-change-page="loadEmails" :limit=2></pagination>
                    </div>
                </div>
            </div>
        </div>
        <b-modal size="lg" ref="linkOptions" :title="'Link options for '+currentEmail.email" ok-only ok-variant="secondary" ok-title="Close">

            <h4># Email Link</h4>
            <b-form-input readonly v-model="currentEmail.link"></b-form-input>

            <h4># HTML Code for the email link</h4>
            <b-form-textarea :rows="3" readonly :value="'<a href=\''+currentEmail.link+'\' target=\'__blank\'>'+currentEmail.hidden_email+'</a>'"></b-form-textarea>
            
            Example: <samp><a :href="currentEmail.link" target="__blank">{{currentEmail.hidden_email}}</a></samp>
        </b-modal>
    </div>
</template>
<script>
    import Form from '@mathewparet/form-error-control';
    import Pagination from 'laravel-vue-pagination';
    import FilterBox from '@mathewparet/vue-filter-box';
    export default {
        components: {
            FilterBox,
            Pagination,
        },
        data()
        {
            return {
                loading: null,
                emails: {},
                currentEmail: {},
                fields: [
                    {
                        key: 'hidden_email',
                        label: 'Email',
                    },
                    {
                        key: 'created_at',
                    },
                    {
                        key: 'action'
                    }
                ],
                filterString: '',
                hideEmailForm: new Form({
                    email: null
                }),
            }
        },
        mounted()
        {
            this.$refs.emailField.focus();
        },
        methods: {
            showLinkOptions(email)
            {
                this.currentEmail = email;
                this.$refs.linkOptions.show();
            },
            deleteEmail(email)
            {
                if(confirm(`This action is irrevokable. You can add back the email later, but the link will changge.\n\nEmail ID: ${email.hidden_email} will be deleted.`))
                {
                    axios.delete(`/api/emails/${email.uuid}`)
                        .then(response => {
                            this.$awn.success(`${email.hidden_email} removed`);
                            this.loadEmails();
                        })
                        .catch(error => this.$awn.alert(error.message));
                }
            },
            addEmail(e)
            {
                e.preventDefault();
                this.hideEmailForm.post('/api/emails')
                    .then(response => {
                        this.$awn.success(response.message);
                        this.loadEmails();
                        this.hideEmailForm.reset();
                    })
                    .catch(error => {
                        this.$awn.alert(error.message);
                    })
                    .finally(() => this.$refs.emailField.focus());
            },
            loadEmails(page=1)
            {
                let filter = this.filterString == null ? '' : this.filterString;
                this.loading = true;
                axios.get(`/api/emails?page=${page}&filter=${filter}`)
                    .then(response => {
                        this.emails = response.data.emails;
                    })
                    .catch(error => this.$awn.alert(error.message))
                    .finally(() => this.loading = false);
            }
        }
    }
</script>
<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>