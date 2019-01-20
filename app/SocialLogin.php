<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialLogin extends Model
{
    protected $fillable =[
        'provider',
        'provider_id'
    ];

    protected $appends = [
        'name',
        'class',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute()
    {
        return config('services.social.'.$this->provider.'.name');
    }

    public function getClassAttribute()
    {
        return config('services.social.'.$this->provider.'.class');
    }
}
