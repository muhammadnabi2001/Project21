<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable=[
        'cart_id',
        'meal_id'
    ];
    public function cart()
    {
        return $this->belongsTo(Cart::class,'cart_id');
    }
    public function meal()
    {
        return $this->belongsTo(Meal::class,'meal_id');
    }
}
