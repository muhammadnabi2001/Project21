<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'img',
        'status',
        'latitude',
        'longitude'
    ];
}
