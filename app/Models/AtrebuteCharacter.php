<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtrebuteCharacter extends Model
{
    protected $fillable=[
        'atrebute_id',
        'character_id',
    ];
    public function options()
    {
        return $this->hasMany(Option::class,'atrebute_character_id');
    }
    public function atrebute()
    {
        return $this->belongsTo(Atrebute::class,'atrebute_id');
    }
    public function character()
    {
        return $this->belongsTo(Character::class,'character_id');
    }
}
