<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'room_id',
        'slug',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
