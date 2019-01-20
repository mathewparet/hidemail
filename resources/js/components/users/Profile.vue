<template>
    <div>
        <div class="card">
            <div class="card-header"><i class="fas fa-user-circle"></i> My Profile</div>
            <div class="card-body">
                <form @submit.prevent="saveProfile" @keydown="profileForm.errors.clear($event.target.name)">

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" :class="{'is-invalid': profileForm.errors.has('name')}" name="name" v-model="profileForm.name" autofocus>

                            <span class="invalid-feedback" style="display: block;">
                                <strong>{{ profileForm.errors.get('name') }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" :class="{'is-invalid': profileForm.errors.has('email')}" name="email" v-model="profileForm.email" autofocus>
                            <b-alert class="mt-3" show variant="warning" v-if="!profileForm.busy && profileForm.email != this.current_email">
                                Once you submit this form, you will receive a verification link in your new email Id. You will need to click on the verification link in the email in order to complete the email ID updation process. Until this is done, your profile / login will still be {{this.current_email}}.
                            </b-alert>
                            <span class="invalid-feedback" style="display: block;">
                                <strong>{{ profileForm.errors.get('email') }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" :class="{'is-invalid': profileForm.errors.has('password')}" name="password" v-model="profileForm.password" placeholder="Required only if you wish to change password">

                            <span class="invalid-feedback" style="display: block;">
                                <strong>{{ profileForm.errors.get('password') }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row" v-if="profileForm.password && profileForm.password.length > 0">
                        <label for="password" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

                        <div class="col-md-6">
                            <input id="password_confirmation" type="password" class="form-control" :class="{'is-invalid': profileForm.errors.has('password_confirmation')}" name="password_confirmation" v-model="profileForm.password_confirmation">

                            <span class="invalid-feedback" style="display: block;">
                                <strong>{{ profileForm.errors.get('password_confirmation') }}</strong>
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label text-md-right">Current Password</label>

                        <div class="col-md-6">
                            <input id="current_password" type="password" class="form-control" :class="{'is-invalid': profileForm.errors.has('current_password')}" name="current_password" v-model="profileForm.current_password" placeholder="Required to save profile">

                            <span class="invalid-feedback" style="display: block;">
                                <strong>{{ profileForm.errors.get('current_password') }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button class="btn btn-primary" type="submit" :disabled="profileForm.busy">Update Profile</button>
                        </div>
                    </div>
                    <span v-if="loadingSocial==false && this.user.id === this.propUserId">
                        <hr>
                        <div class="form-group row mb-0" v-for="provider in this.socialProviders" :key="provider.id">
                            <div class="col-md-6 offset-md-4" v-if="provider.enabled === true">
                                <a class="btn btn-default" v-if="!providerLinked[provider.id]" :href="'/login/'+provider.id"><i :class="provider.class"></i> Link {{provider.name}} account</a>
                                <a class="btn btn-default" v-else @click="delinkSocialAccount(provider.id)"><i :class="provider.class"></i> Delink {{provider.name}}</a>
                            </div>
                        </div>
                    </span>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    import Form from "@mathewparet/form-error-control";
    import { mapState, mapMutations } from 'vuex';

    export default {
        props: ['id'],
        data()
        {
            return {
                profileForm: new Form({
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                    current_password: null,
                }),
                current_email: null,
                user_id: null,
                socialProviders: {},
                loadingSocial: true,
                socialLogins: [],
            };
        },
        computed: {
            ...mapState(['user']),
            propUserId()
            {
                return this.id ? this.id : this.user.id
            },
            providerLinked()
            {
                let isLinked = {};
                this.socialLogins.forEach(login => {
                    isLinked[login.provider] = true;
                });
                return isLinked;
            }
        },
        mounted()
        {
            this.profileForm.reset();
            this.fetchUserDetails(this.propUserId);
            this.getSocialProviders();
        },
        beforeRouteUpdate (to, from, next) {
            this.fetchUserDetails(this.user.id);
            next();
        },
        methods: {
            ...mapMutations(['setUser']),
            getSocialProviders()
            {
                axios.get('/api/social')
                    .then(response => {
                        this.socialProviders = response.data.social;
                    })
                    .catch(error => this.$awn.alert(error.message))
                    .finally(() => this.loadingSocial = false);
            },
            delinkSocialAccount(provider)
            {
                let login = this.socialLogins.find(login => login.provider === provider);

                axios.delete(`/api/users/${this.user_id}/social/${login.id}`)
                    .then(response => {
                        this.$awn.success(response.data.message);

                        if(response.data.user.id === this.user.id)
                            this.setUser(response.data.user);

                        this.socialLogins = response.data.user.social_logins;
                    })
                    .catch(error => {
                        this.$awn.alert(error.messge);
                    });
            },
            populateForm(user)
            {
                this.profileForm.name = user.name;
                this.profileForm.email = user.email;
                this.current_email = user.email;
                this.user_id = user.id
                this.socialLogins = user.social_logins;
                let self = this;

                this.profileForm.busy = false;
            },
            getOtherUserInformation(userId)
            {
                axios.get(`/api/users/${userId}`)
                        .then(response => {
                            this.populateForm(response.data.user);
                        })
                        .catch(error => {
                            if(error.response.status == 403)
                            this.$awn.alert("You are not authorized to to load this profile.");
                            this.$router.push({name: 'emails.index'});
                        })
                        .finally(() => {
                            this.profileForm.busy = false;
                        });
            },
            fetchUserDetails(userId)
            {
                this.profileForm.busy = true;
                
                if(this.user.id === userId)
                    this.populateForm(this.user);
                else
                    this.getOtherUserInformation(userId);
            },
            saveProfile()
            {
                this.profileForm.patch(`/api/users/${this.user_id}`)
                    .then(response => {                        
                        if(response.user.id === this.user.id)
                            this.setUser(response.user);
                        
                        this.$router.go(-1);
                        this.$awn.success(response.message);
                    })
                    .catch(error => {
                        this.$awn.alert(error.message);
                    });
            }
        }
    }
</script>
