<template>
    <div>
        <form @submit.prevent="addEmail" @keydown="hideEmailForm.errors.clear($event.target.name)">                                
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="input-group">
                        <input type="email" ref="emailField" class="form-control form-control-lg" :class="{'is-invalid': hideEmailForm.errors.has('email'), 'border-primary': !hideEmailForm.errors.has('email')}" v-model="hideEmailForm.email" name="email" :disabled="hideEmailForm.busy" placeholder="Email ID to be hidden. E.g. johndoe@example.com">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-lg" @click.prevent="addEmail" :disabled=this.hideEmailForm.busy>Hide Email</button>
                        </div>
                    </div>
                    <span class="invalid-feedback" style="display: block;">
                        <strong>{{ hideEmailForm.errors.get('email') }}</strong>
                    </span>
                </div>
            </div>
            <vue-recaptcha ref="recap" sitekey="6Lfhr4gUAAAAADUsCoLtBpcsX5JEJtrcAFRsO7VS" size="invisible" @verify="onVerified" type="invisible"></vue-recaptcha>
        </form>
        <b-modal size="lg" ref="linkOptions" ok-only ok-variant="secondary" ok-title="Close">
            <div slot="modal-title" class="text-dark">Hidden Email</div>
            <div class="text-left text-dark">
                <p>
                    You can use the below link wherever your email is supposed to be displayed.
                    <b-form-input readonly v-model="currentEmail.link"></b-form-input>
                </p>
                <p>
                    You can display an obfuscated version of your email address which links to the above URL. Since the full email address is no longer displayed, spam bots will not be able to crawl and index your emails. When the link is clicked, actual email ID will be displayed <em>after reCaptcha verifies that the visitor is a human</em>.
                    <h3>Sample Code</h3>
                    <b-form-textarea :rows="3" readonly :value="'<a href=\''+currentEmail.link+'\' target=\'__blank\'>'+currentEmail.hidden_email+'</a>'"></b-form-textarea>
                
                    Example: <samp><a :href="currentEmail.link" target="__blank">{{currentEmail.hidden_email}}</a></samp>
                </p>
                <b-alert show variant="info">
                    If you have multiple email IDs to hide, you may want to consider registering for a FREE account. Once you register for an account, you will be able to use our API to programatically generate the email links as needed.
                </b-alert>
            </div>
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