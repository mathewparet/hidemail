<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{config('app.name')}}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="{{ mix('css/landing.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{mix('js/manifest.js')}}" defer></script>
    <script src="{{mix('js/vendor.js')}}" defer></script>
    <script src="{{mix('js/app.js')}}" defer></script>
    <script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer></script>


    </head>

    <body>

    <!-- Navigation -->
    <nav class="navbar navbar-light bg-light static-top">
    <div class="container">
        <a class="navbar-brand" href="{{config('app.url')}}">{{config('app.name')}}</a>
        <div class="float-right links">
            <a href="https://documenter.getpostman.com/view/5815935/RznJoGx5" target="__blank">API Documentation</a>
            @auth
                <a href="{{ url('/home') }}">{{auth()->user()->name}}</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    </div>
    </nav>

    <!-- Masthead -->
    <header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
        <div class="row" id="guestHideMailApp">
            <div class="col-xl-9 mx-auto">
                <h1 class="mb-5">Hide your email from Spammers. Avoid being indexed.</h1>
            </div>
            <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
                <div class="content">
                    @auth
                        <p>It seems like you are already logged in. Please go to your <a href="/home" class="text-warning">dashboard</a> to hide more email IDs</p>
                    @else
                        <guest-hide-email-id/>
                    @endauth
                </div>
            </div>
            <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
                <h2 class="mb-5">
                    <animate-number
                          from="0" 
                          :to="{{$stats}}" 
                          duration="1000" 
                          easing="easeOutQuad"
                          :formatter="formatter"
                        >                                                
                        </animate-number> email IDs hidden (and counting...)</h2>
            </div>
        </div>
    </div>
    </header>

    <!-- Icons Grid -->
    <section class="features-icons bg-light text-center">
    <div class="container">
        <div class="row">
        <div class="col-lg-4">
            <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
            <div class="features-icons-icon d-flex">
                <i class="icon-flag m-auto text-primary"></i>
            </div>
            <h3>Spam Protection</h3>
            <p class="lead mb-0">
                Hide your email from bots / crawlers that index email IDs posted publicly on the internet, so that they are protected from spam or phishing.
            </p>
        </div>
        </div>
        <div class="col-lg-4">
            <div class="features-icons-item mx-auto mb-0 mb-lg-3">
            <div class="features-icons-icon d-flex">
                <i class="icon-rocket m-auto text-primary"></i>
            </div>
            <h3>API Access</h3>
            <p class="lead mb-0">We support OAuth 2.0. With API support, you can hide email IDs, search hidden email IDs and delete them programatically. You can even create a personal access token if you do not wish to use a complete OAuth 2.0 authentication.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
            <div class="features-icons-icon d-flex">
                <i class="icon-shield m-auto text-primary"></i>
            </div>
            <h3>reCAPTCHA</h3>
            <p class="lead mb-0">
                reCAPTCHA is a free service from Google that protects your website from spam and abuse. All reveal requests are validated by reCAPTCHA before the email ID is displayed on screen.
            </p>
        </div>
        </div>
        </div>
    </div>
    </section>

    <!-- Image Showcases -->
    <section class="showcase">
        <div class="container-fluid p-0">
            <div class="row no-gutters">

            <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('img/email-protection.jpg');"></div>
            <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                <h2>How It Works</h2>
                <p class="lead mb-0">
                    When an email ID is added to our system we return an email link and a masked email ID. The masked email ID can be shown to the user with a hyperlink that we generate. When the user clicks on the link, reCAPTCHA will analyse the request and show the email ID, if the requester is not a bot.  E.g. Go ahead, try this: <a href="{{$owner->link}}">{{$owner->hidden_email}}</a>
                </p>
            </div>
            </div>
            <div class="row no-gutters">
            <div class="col-lg-6 text-white showcase-img" style="background-image: url('img/encryption.jpg');"></div>
            <div class="col-lg-6 my-auto showcase-text">
                <h2>Encryption</h2>
                <p class="lead mb-0">
                    All email IDs being hidden are stored with enterprise grade AES 256 encryption. This makes sure that even if a hacker gains access to our database, retrieving the email IDs would be near to impossible.
                </p>
            </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-light">
    <div class="container">
        <div class="row">
        <div class="col-lg h-100 text-center my-auto">
            @include('layouts/footer')
        </div>
        </div>
    </div>
    </footer>

    </body>
</html>
