<template>
    <div>
        <form @submit.prevent="addEmail" @keydown="hideEmailForm.errors.clear($event.target.name)">                                
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="input-group">
                        <input type="email" ref="emailField" class="form-control" :class="{'is-invalid': hideEmailForm.errors.has('email'), 'border-primary': !hideEmailForm.errors.has('email')}" v-model="hideEmailForm.email" name="email" :disabled="hideEmailForm.busy" placeholder="Email ID to be hidden. E.g. johndoe@example.com">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary btn-sm" @click.prevent="addEmail" :disabled=this.hideEmailForm.busy>Add Email</button>
                        </div>
                    </div>
                    <span class="invalid-feedback" style="display: block;">
                        <strong>{{ hideEmailForm.errors.get('email') }}</strong>
                    </span>
                </div>
            </div>
            <vue-recaptcha ref="recap" sitekey="6Lfhr4gUAAAAADUsCoLtBpcsX5JEJtrcAFRsO7VS" size="invisible" @verify="onVerified" type="invisible"></vue-recaptcha>
        </form>
        <b-modal size="lg" ref="linkOptions" title="Hidden Email" ok-only ok-variant="secondary" ok-title="Close">

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
    import VueRecaptcha from 'vue-recaptcha';
    export default {
        components: {
            VueRecaptcha,
        },
        data()
        {
            return {
                hideEmailForm: new Form({
                    email: null,
                    recaptcha: null,
                }),
                currentEmail: {}
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
            addEmail(e)
            {
                e.preventDefault();
                this.$refs.recap.execute();
            },
            onVerified(response)
            {
                this.hideEmailForm.recaptcha = response;

                this.hideEmailForm.post('/api/guestEmail')
                    .then(response => {
                        this.$awn.success(response.message);
                        this.hideEmailForm.reset();
                        this.showLinkOptions(response.email);
                    })
                    .catch(error => {
                        this.$awn.alert(error.message);
                    })
                    .finally(() => {
                        this.$refs.recap.reset();
                    });
            }
        }
    }
</script>
<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>