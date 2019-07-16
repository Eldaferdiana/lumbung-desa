<?php
/**
 * Created by PhpStorm.
 * User: Alfarady
 * Date: 2/23/2019
 * Time: 10:53 AM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    public $timestamps = false;

    protected $fillable = [
        'product_image'
    ];

    protected $hidden = [
        'id', 'product_id'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}