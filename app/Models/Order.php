<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=[
        'user_id',
        'status',
        'shipping_address',
        'totalprice',
        'latitude',
        'longitude',
        'payment_status'
    ];
    public function orderitems()
    {
        return $this->hasMany(OrderItem::class,'order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
