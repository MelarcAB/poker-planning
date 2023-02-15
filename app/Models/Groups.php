<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UserGroups;
use \Illuminate\Database\Eloquent\SoftDeletes;

//rooms
use App\Models\Room;

class Groups extends Model
{
    use HasFactory;
    use SoftDeletes;

    //soft delete


    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'deck_id',
        'user_id'
    ];

    //soft delete
    protected $dates = ['deleted_at'];



    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups', 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'group_id')->orderBy('created_at', 'desc');
    }

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }
}
