<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyCsrfTokenCustom;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

use App\Models\Groups;
//general api calls
//llamadas desde la misma aplicación
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
                $group_arr['slug'] = $group->slug;
                //numero de usuarios
                $group_arr['users'] = $group->users->count();
                //username del creador
                $group_arr['creator'] = $group->user->username;
                //añadir al array
                $groups_arr[] = $group_arr;
            }
            //devolver los grupos con mensaje de exito
            return response()->json(['message' => 'Grupos encontrados', 'groups' => $groups_arr]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al buscar el grupo'], 500);
        }
    }
}
