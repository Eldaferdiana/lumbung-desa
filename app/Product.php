<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';

    protected $fillable = [
        'id_category', 'product_name', 'product_desc', 'product_price', 'product_stok', 'expired_at'
    ];

    public function seller()
    {
        return $this->belongsTo('App\User', 'id_seller');
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
