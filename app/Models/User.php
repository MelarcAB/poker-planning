<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserType;
use App\Models\UserGroups;
use App\Models\Groups;
use \PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;
//deck
use App\Models\Deck;

class User extends Authenticatable implements JWTSubject

{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function user_type()
    {
        return $this->belongsTo(UserType::class);
    }


    //obtener todos los grupos a relacionados con el usuario de la tabla user_groups
    public function groups()
    {
        return $this->belongsToMany(Groups::class, 'user_groups', 'user_id', 'group_id');
    }

    //obtener todos los grupos creados por el usuario (user_id en Groups)
    public function groups_created()
    {
        return $this->hasMany(Groups::class, 'user_id');
    }

    public function belongsToGroup($slug)
    {

        $group = Groups::where('slug', $slug)->firstOrFail();

        if ($this->groups->contains($group)) {
            return true;
        }
        return false;
    }

    public function isGestor()
    {
        if ($this->user_type->name == 'gestor' || $this->user_type->name == 'admin') {
            return true;
        }
        return false;
    }

    //jwt
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    //deck
    public function decks()
    {
        return $this->hasMany(Deck::class);
    }



    //decks
    public function decks_disponibles()
    {
        //devolver decks del usuario + decks publicos
        return Deck::where('user_id', $this->id)->orWhere('public', true)->get();
    }




    //invitaciones enviadas (campo sender)
    public function invitations_sent()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    //invitaciones recibidas (campo receiver)
    public function invitations_received()
    {
        return $this->hasMany(Invitation::class, 'receiver_id');
    }
}
