@component('mail::message')
# Hello {{$user->name}},

We need to verify your new email address before we can update your profile with the new email address. Click on the below button to verify your email address. It expires in {{config('app.sign_expiry.long')}} hours!

If you've changed your mind or if you do not wish to change your registered email to this, you can simply ignore this email.

Your current email address on file is: **{{$user->email}}**

After you click on the below button your email address on file will be updated to: **{{$email}}**

**Note:** *If you are asked to login, you will need to use your old email address to authenticate yourself for one final time, since the email verification will take place only after authentication. However, post verification your new login will be your new email address.*

@component('mail::button', ['url' => $url])
Verify my email address
@endcomponent


Thanks,<br>
Team {{ config('app.name') }}
@endcomponent
