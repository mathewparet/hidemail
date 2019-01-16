<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RBennett\ModelEncryption\HasEncryptedAttributes;

class Email extends Model
{
    use HasEncryptedAttributes;

    protected $fillable = ['email'];

    protected $hidden = ['email','updated_at','user_id','id', 'email_bi'];

    protected $appends = ['link','hidden_email'];

    public function __construct($attributes = [])
    {
        $this->uuid = Str::uuid();

        parent::__construct($attributes);
    }

    protected $casts = [
        'created_at' => 'datetime:M d, Y H:i:s'
    ];

    protected $encrypted = [
        'email' =>
            ['type' => 'string', 'hasBlindIndex' => 'email_bi'],
    ];

    protected $hashed = [];

    public function user()
    {
        return $this->belongsTo(User::class);
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
        return $query->whereBI(['email'=>strtolower($value)]);
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
