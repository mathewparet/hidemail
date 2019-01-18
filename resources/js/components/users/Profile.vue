<template>
    <div>
        <div class="card">
            <div class="card-header"><i class="fas fa-user"></i> My Profile</div>
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
                            <b-alert class="mt-3" show variant="warning" v-if="!profileForm.busy && profileForm.email != this.user.email">
                                Once you submit this form, you will receive a verification link in your new email Id. You will need to click on the verification link in the email in order to complete the email ID updation process. Until this is done, your profile / login will still be {{this.user.email}}.
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
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    import Form from "@mathewparet/form-error-control";
    import { mapState, mapMutations } from 'vuex';

    export default {
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
            };
        },
        computed: {
            ...mapState(['user']),
        },
        mounted()
        {
            this.profileForm.reset();
            this.fetchUserDetails();
        },
        methods: {
            ...mapMutations(['setUser']),
            fetchUserDetails()
            {
                this.profileForm.busy = true;
                this.profileForm.name = this.user.name;
                this.profileForm.email = this.user.email;
                this.profileForm.busy = false;
            },
            saveProfile()
            {
                this.profileForm.post('/api/profile')
                    .then(response => {
                        this.$awn.success(response.message);
                        this.setUser(response.user);
                        this.$router.go(-1);
                    })
                    .catch(error => {
                        this.$awn.alert(error.message);
                    });
            }
        }
    }
</script>
