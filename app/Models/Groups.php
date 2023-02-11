<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UserGroups;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Groups extends Model
{
    use HasFactory;
    use SoftDeletes;

    //soft delete


    protected $fillable = [
        'name',
        'slug',
        'code',
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
}
