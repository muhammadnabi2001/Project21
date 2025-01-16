<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remember extends Model
{
    protected $fillable = [
        'chat_id',
        'step',
        'name',
        'email',
        'password',
        'img',
        'companyname',
        'longitude',
        'latitude',
        'companyimg',
    ];
}
