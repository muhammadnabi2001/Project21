<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atrebute extends Model
{
    protected $fillable=[
        'category_id',
        'name'
    ];
    public function atrebute_character()
    {
        return $this->hasMany(AtrebuteCharacter::class,'atrebute_id');
    }
}
