<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Email extends Model
{
    protected $fillable = ['email'];

    protected $hidden = ['email','updated_at','user_id','id'];

    protected $appends = ['link','hidden_email'];

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

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = encrypt($value);
    }

    public function getEmailAttribute()
    {
        return decrypt($this->attributes['email']);
    }

    public function getRouteKeyName()
    {
        return "uuid";
    }

    public function getHiddenEmailAttribute()
    {
        return $this->obfuscate($this->email);
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
