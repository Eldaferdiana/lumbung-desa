<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $table = 'payment';

    protected $fillable = [
        'nama_payment', 'nomer_payment'
    ];
    
    public $timestamps = false;
}
