<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $fillable=[
        'chat_id',
        'step',
        'name',
        'email',
        'password',
        'img'
    ];
}