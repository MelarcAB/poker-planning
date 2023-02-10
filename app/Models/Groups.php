<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UserGroups;

class Groups extends Model
{
    use HasFactory;




    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups', 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
