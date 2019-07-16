<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class User extends Model implements Authenticatable
{
    use AuthenticableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $incrementing = false;
    
    protected $fillable = [
        'id', 'msisdn', 'name', 'ava_url'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getAddress(){
        return $this->hasOne('App\Address','id_user');
    }

    public function products(){
        return $this->hasMany('App\Product','id_seller');
    }

    public function getConversation(){
        return $this->hasMany('App\Conversation', ['seller_id', 'buyer_id']);
    }

    public function getTransaction(){
        return $this->hasMany('App\Transaction','id_buyer');
    }
}
