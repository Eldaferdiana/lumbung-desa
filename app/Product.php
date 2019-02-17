<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'products';

    protected $fillable = [
        'product_name', 'product_desc', 'product_price'
    ];

    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

}
