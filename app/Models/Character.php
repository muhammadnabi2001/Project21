<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable=[
        'name'
    ];
    public function atrebute_character()
    {
        return $this->hasMany(AtrebuteCharacter::class,'character_id');
    }
}
