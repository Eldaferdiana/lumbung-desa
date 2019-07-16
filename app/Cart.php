<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $table = 'cart';

    protected $fillable = [
        'id_user', 'id_transaction', 'id_product', 'quantity', 'priceTotal', 'id_seller'
    ];

    public function getTransaction()
    {
        return $this->belongsTo('App\Transaction');
    }

    public function products()
    {
        return $this->belongsTo('App\Product', 'id');
    }
    
}
