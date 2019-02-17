<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'address';

    protected $fillable = [
        'country', 'state', 'city', 'kecamatan', 'desa', 'road'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
