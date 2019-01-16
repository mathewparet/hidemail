<?php

return [

   /**
    * Recatcha configuration - generate one at https://www.google.com/recaptcha/admin
    */

    'site' => env('RECAPTCHA_SITEKEY'),
    'secret' => env('RECAPTCHA_SECRET'),
    'enabled' => env('RECAPTCHA_ENABLED', true) ? 'true' : 'false',

];
