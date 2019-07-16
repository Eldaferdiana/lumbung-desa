<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{

    protected $table = 'conversation';

    protected $fillable = [
        'buyer_id', 'seller_id'
    ];

    public function getBuyer()
    {
        return $this->belongsTo('App\User');
    }

    public function getSeller()
    {
        return $this->belongsTo('App\User');
    }

    public function getMessages()
    {
        return $this->hasMany('App\Messages', 'id_conversation');
    }
}
