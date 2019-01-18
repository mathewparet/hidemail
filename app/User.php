<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

use App\Events\UserUpdating;
use App\Events\UserUpdated;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_hash', 'suspended',
    ];

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function addEmail(Email $email)
    {
        return $this->emails()->save($email);
    }

    public function owns($model)
    {
        return $model->user_id === $this->id;
    }

    public function setEmailAttribute($value)
    {
        $small_email = strtolower($value);
        
        $this->attributes['email_hash'] = sha1($small_email);
        $this->attributes['email'] = encrypt($small_email);
    }

    public function getEmailAttribute()
    {
        return decrypt($this->attributes['email']);
    }
}
