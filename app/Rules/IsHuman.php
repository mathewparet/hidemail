<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use GuzzleHttp\Client;

use App;

class IsHuman implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(config('recaptcha.enabled') == false)
            return true;

        $client = new Client();

        $response = $client->post('https://www.google.com/recaptcha/api/siteverify',
                ['form_params'=>
                    [
                        'secret'=>config('recaptcha.secret'),
                        'response'=>$value
                    ]
                ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You do not seem to be human';
    }
}
