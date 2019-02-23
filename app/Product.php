<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';

    protected $fillable = [
        'id_category', 'product_name', 'product_desc', 'product_price', 'expired_at'
    ];

    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function getProductImage()
    {
        return $this->hasMany('App\ProductImage');
    }

}
