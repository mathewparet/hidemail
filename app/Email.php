<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Email extends Model
{
    protected $fillable = ['email'];

    function __construct($attributes = [])
    {
        $this->uuid = Str::uuid();

        parent::__construct($attributes);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function setEmailAttribute($value)
    {
        $this->attributes['email'] = encrypt($value);
    }

    function getEmailAttribute()
    {
        return decrypt($this->attributes['email']);
    }
}
