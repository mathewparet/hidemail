<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Email extends Model
{
    protected $fillable = ['email'];

    protected $appends = ['link'];

    public function __construct($attributes = [])
    {
        $this->uuid = Str::uuid();

        parent::__construct($attributes);
    }

    protected $casts = [
        'created_at' => 'datetime:M d, Y H:i:s'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // function setEmailAttribute($value)
    // {
    //     $this->attributes['email'] = encrypt($value);
    // }

    public function getRouteKeyName()
    {
        return "uuid";
    }

    public function getEmailAttribute()
    {
        return $this->obfuscate($this->attributes['email']);
    }

    public function scopeLike($query, $value)
    {
        return $query->where('email','like','%'.strtolower($value).'%');
    }

    public function getLinkAttribute()
    {
        return __(':url/emails/:uuid', [
            'url'=>config('app.url')
            , 'uuid'=>$this->uuid]
        );
    }

    private function obfuscate($email)
    {
        $em   = explode("@",$email);
        $name = implode(array_slice($em, 0, count($em)-1), '@');
        $len  = floor(strlen($name)/2);

        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);   
    }

}
