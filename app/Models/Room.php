<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//room status
use App\Models\RoomStatus;
//user
use App\Models\User;
//group
use App\Models\Groups;
//entry
use App\Models\Entry;
//points
use App\Models\Points;


class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'group_id',
        'user_id',
        'room_status_id',
        'slug',
    ];

    public function group()
    {
        return $this->belongsTo(Groups::class);
    }

    public function status()
    {
        return $this->belongsTo(RoomStatus::class, 'room_status_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id',);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function points()
    {
        return $this->hasMany(Points::class);
    }
}
