<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable=[
        'name',
        'email',
        'company_id',
        'role',
        'status',
        'password',
        'chat_id'
        
    ];
}
