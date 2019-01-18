<template>
    <form id="emailRevealForm" @submit.prevent="revealEmailId">
        <vue-recaptcha ref="recap" sitekey="6Lfhr4gUAAAAADUsCoLtBpcsX5JEJtrcAFRsO7VS" size="invisible" @render="autoSubmit" @verify="onVerified" type="invisible"></vue-recaptcha>
        <div class="card">
            <div class="card-body">
                <div class="card-text text-center align-items-center justify-content-center">
                    <div class="lds-css ng-scope  justify-content-center align-items-center" v-if="this.checking == true">
                        <div style="width:100%;height:100%" class="lds-eclipse">
                            <div></div>
                        </div>
                        <h1>Trying to automatically detect whether you are human, you maybe asked some questions...</h1>
                    </div>
                    <div v-else>
                        <h1 v-if="this.actualEmail">{{this.actualEmail}}</h1>
                        <b-alert show v-else-if="this.status != null" variant="danger">
                            <h1>{{this.status}}</h1>
                        </b-alert>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<style type="text/css">
@keyframes lds-eclipse {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  50% {
    -webkit-transform: rotate(180deg);
    transform: rotate(180deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-webkit-keyframes lds-eclipse {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  50% {
    -webkit-transform: rotate(180deg);
    transform: rotate(180deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
.lds-eclipse {
  position: relative;
}
.lds-eclipse div {
  position: absolute;
  -webkit-animation: lds-eclipse 0.3s linear infinite;
  animation: lds-eclipse 0.3s linear infinite;
  width: 120px;
  height: 120px;
  top: 40px;
  left: 40px;
  border-radius: 50%;
  box-shadow: 0 4px 0 0 #cad1fb;
  -webkit-transform-origin: 60px 62px;
  transform-origin: 60px 62px;
}
.lds-eclipse {
  width: 78px !important;
  height: 78px !important;
  -webkit-transform: translate(-39px, -39px) scale(0.39) translate(39px, 39px);
  transform: translate(-39px, -39px) scale(0.39) translate(39px, 39px);
}
</style>

<script>
    import VueRecaptcha from 'vue-recaptcha';
    export default {
        components: {
            VueRecaptcha,
        },
        data()
        {
            return {
                actualEmail: null,
                checking: true,
                status: null,
            };
        },
        props: {
            email: {
                required: true,
                type: String,
            }
        },
        methods: {
            onVerified(response)
            {
                this.checking = true;
                axios.post(`/api/emails/${this.email}`, {
                    'g-recaptcha-response': response
                })
                .then(response => {
                    this.actualEmail = response.data.email;
                })
                .catch(error => {
                    if(error.response.data.errors)
                        this.status = error.response.data.errors['g-recaptcha-response'][0];
                    else
                        this.status = error.message;
                })
                .finally(() => {this.checking = false;});
            },
            autoSubmit(id)
            {
                this.$refs.recap.execute();
            }
        }
    }
</script>

