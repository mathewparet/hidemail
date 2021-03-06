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
        'password', 'remember_token', 'email_hash',
    ];

    protected $appends = [
        'verified'
    ];

    protected $with = [
        'socialLogins'
    ];

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function addEmail(Email $email)
    {
        return $this->emails()->save($email);
    }

    public function getVerifiedAttribute()
    {
        return blank($this->email_verified_at);
    }

    /**
     * Check if a model is owned by the user
     * 
     * @param Illuminate\Database\Eloquent\Model $model
     * 
     * @return boolean
     */
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

    public function scopeLike($query, $filter)
    {
        return $query->where('name','like','%'.$filter.'%')
            ->orWhere('email_hash',sha1($filter));
    }

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    /**
     * Add a new social login and attach it to the user
     * 
     * @param \App\SocialLogin $social_login
     * 
     * @return \App\SocialLogin 
     */
    public function addSocialLogin(SocialLogin $social_login)
    {
        return $this->socialLogins()->save($social_login);
    }
}
