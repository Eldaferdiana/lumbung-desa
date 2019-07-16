<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'address';

    protected $fillable = [
        'country', 'state', 'city', 'kecamatan', 'desa', 'road'
    ];

    protected $hidden = [
        'id', 'id_user', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
