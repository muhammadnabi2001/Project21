<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable=[
        'order_id',
        'meal_id',
        'quantity'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function meal()
    {
        return $this->belongsTo(Meal::class,'meal_id');
    }
}
