<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{

    protected $table = 'messages';

    protected $fillable = [
        'conversation_id', 'sender_id', 'message'
    ];

    public function conversation()
    {
        return $this->belongsTo('App\Conversation');
    }

    public function getSender()
    {
        return $this->belongsTo('App\User');
    }

}
