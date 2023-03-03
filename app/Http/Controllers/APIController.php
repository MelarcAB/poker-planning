<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyCsrfTokenCustom;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

use App\Models\Groups;
//general api calls
//llamadas desde la misma aplicación

use App\Models\Invitation;
use \App\Models\User;

class APIController extends Controller
{


    public function __construct()
    {
    }

    public function updateGroupCode(Request $request)
    {
        try {

            //validar
            $this->validate($request, [
                'code' => 'required|string|max:150',
                'slug' => 'required|string|max:500',
            ]);

            //obtener usuario que hace la peticion 
            $user = $request->user();
            //obtener el codigo del grupo
            $code = $request->input('code');
            //obtener el slug del grupo
            $slug = $request->input('slug');
            //obtener el grupo
            $group = Groups::where('slug', $slug)->first();
            //comprobar que el grupo existe
            if (!$group) {
                return response()->json(['message' => 'El grupo no existe'], 404);
            }
            //comprobar que el usuario pertenece al grupo

            //actualizar el codigo del grupo
            $group->code = $code;
            $group->save();

            return response()->json(['message' => 'Contraseña actualizada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar la contraseña'], 500);
        }
    }

    public function checkCode(Request $request)
    {
        //validar
        $this->validate($request, [
            'code' => 'required|string|max:150',
            'slug' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $code = $request->input('code');
        $slug = $request->input('slug');
        $group = Groups::where('slug', $slug)->first();
        //comprobar que el grupo existe
        if (!$group) {
            //devolver error
            return response()->json(['message' => 'El grupo no existe'], 404);
        }
        //comprobar que el codigo es correcto
        if ($group->code != $code) {
            return response()->json(['message' => 'El código es incorrecto'], 403);
        }

        //comprobar si el usuario pertenece al grupo
        if ($user->groups->contains($group)) {
            //devolver error
            return response()->json(['message' => 'Ya perteneces al grupo'], 403);
        }

        //añadir el usuario al grupo
        $user->groups()->attach($group->id);
        //devolver mensaje de exito
        //refrescar la pagina
        return response()->json(['message' => 'Acceso concedido']);
    }

    //searchGroup GET
    public function searchGroup(Request $request)
    {
        try {
            //obtener usuario que hace la peticion 
            $user = $request->user();
            //obtener el codigo del grupo
            $code = $request->input('q');
            //obtener los
            $groups = Groups::where('name', $code)->get();
            //comprobar que el grupo existe
            if ($groups->count() < 1) {
                return response()->json(['message' => 'No hay grupos con ese nombre '], 404);
            }
            $groups_arr = [];

            foreach ($groups as $group) {
                //obtener la info necesaria
                $group_arr = [];
                $group_arr['name'] = $group->name;
                $group_arr['description'] = $group->description;
                $group_arr['slug'] = $group->slug;
                //numero de usuarios
                $group_arr['users'] = $group->users->count();
                //username del creador
                $group_arr['creator'] = $group->user->username;
                //group url
                $group_arr['url'] = route('group', ['slug' => $group->slug]);
                //añadir al array
                $groups_arr[] = $group_arr;
            }
            //devolver los grupos con mensaje de exito
            return response()->json(['message' => 'Grupos encontrados', 'groups' => $groups_arr]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al buscar el grupo'], 500);
        }
    }


    //invitate POST
    public function invitate(Request $request)
    {
        try {

            //validar group_slug
            $this->validate($request, [
                'group_slug' => 'required|string|max:500',
                'username' => 'required|string|max:500',
            ]);

            //sender from auth
            $sender = $request->user();
            //obtener el slug del grupo
            $group_slug = $request->input('group_slug');
            //obtener el grupo
            $group = Groups::where('slug', $group_slug)->first();
            //comprobar que el grupo existe
            if (!$group) {
                return response()->json(['message' => 'El grupo no existe'], 404);
            }
            //obtener el usuario al que se le va a invitar
            $username = $request->input('username');

            //verificar que no es el mismo usuario (verificar con to_lower)
            if (strtolower($sender->username) == strtolower($username)) {
                return response()->json(['message' => 'No puedes invitarte a ti mismo'], 403);
            }


            //comprobar que el usuario existe
            $receiver = User::where('username', $username)->first();
            if (!$receiver) {
                return response()->json(['message' => 'El usuario no existe'], 404);
            }

            //comprobar que el usuario no pertenece al grupo
            if ($receiver->groups->contains($group)) {
                return response()->json(['message' => 'El usuario ya pertenece al grupo'], 403);
            }

            //comprobar que no hay una invitacion pendiente
            $invitation = Invitation::where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id)
                ->where('group_id', $group->id)
                //status = pending
                ->where('status', "LIKE", 'pending')
                ->first();
            if ($invitation) {
                return response()->json(['message' => 'Ya hay una invitación pendiente'], 403);
            }

            //crear la invitacion
            $invitation = new Invitation();
            $invitation->sender_id = $sender->id;
            $invitation->receiver_id = $receiver->id;
            $invitation->group_id = $group->id;
            $invitation->status = 'pending';
            $invitation->save();
            return response()->json(['message' => 'Invitación enviada correctamente']);
        } catch (\Exception $e) {
            //devolver error
            // return response()->json(['message' => $e->getMessage()], 500);
            return response()->json(['message' => 'Error al invitar al usuario'], 500);
        }
    }

    public function manageInvitation(Request $request)
    {
        try {
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error gestionar la invitación'], 500);
        }
    }
}
