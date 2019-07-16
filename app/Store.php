<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{

    protected $table = 'store';

    protected $fillable = [
        'id_address', 'store_name', 'store_desc', 'store_status'
    ];

    protected $hidden = [
        'id_address', 'id_user', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getProduct()
    {
        return $this->hasMany('App\Product','id_store');
    }

}
