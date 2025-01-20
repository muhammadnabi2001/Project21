<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable=[
        'chat_id',
        'date',
        'summa',
        'count'
    ];
    public function items()
    {
        return $this->hasMany(CartItem::class,'cart_id');
    }
}
