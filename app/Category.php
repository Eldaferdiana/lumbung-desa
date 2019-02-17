<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'category';

    protected $fillable = [
        'category_name'
    ];

    public function getProduct()
    {
        return $this->hasMany('App\Product','id_category');
    }

}
