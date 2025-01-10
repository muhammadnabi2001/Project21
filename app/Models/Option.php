<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable=[
        'element_id',
        'atrebute_character_id'
    ];
    public function element()
    {
        return $this->belongsTo(Element::class,'element_id');
    }
    public function atrebute_character()
    {
        return $this->belongsTo(AtrebuteCharacter::class,'atrebute_character_id');
    }
}
