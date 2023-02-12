<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomStatus extends Model
{
    use HasFactory;

    //tabla
    protected $table = 'room_status';

    //campos
    protected $fillable = [
        'name',
    ];
}
