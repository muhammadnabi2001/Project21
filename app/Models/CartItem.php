<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable=[
        'cart_id',
        'meal_id'
    ];
}
