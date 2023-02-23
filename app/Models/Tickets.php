<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//tickets 
use App\Models\Room;
use App\Models\TicketsVotation;

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

    public function votations()
    {
        return $this->hasMany(TicketsVotation::class, 'ticket_id', 'id');
    }
}
