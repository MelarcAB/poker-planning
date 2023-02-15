<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'public',
        'user_id',
        //image
        'image',
    ];



    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    //user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
