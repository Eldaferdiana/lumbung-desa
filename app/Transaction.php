<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $table = 'transaction';

    protected $fillable = [
        'id_buyer', 'id_payment', 'price_total', 'price_unique', 'checked_out', 'paid', 'shipped', 'delivered', 'cencelled'
    ];

    public function getBuyer()
    {
        return $this->belongsTo('App\User');
    }

    public function getPayment()
    {
        return $this->belongsTo('App\Payment');
    }

    public function getCart()
    {
        return $this->hasMany('App\Cart','id_transaction');
    }

}
