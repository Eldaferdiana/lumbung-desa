<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'store';

    protected $fillable = [
        'store_name', 'store_desc', 'store_status'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
