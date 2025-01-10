<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable=[
        'category_id',
        'name',
        'description'
    ];
    public function elements()
    {
        return $this->hasMany(Element::class,'product_id');
    }
}
