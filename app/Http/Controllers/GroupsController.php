<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Groups;
use Illuminate\Foundation\Validation\ValidatesRequests;
//str
use Illuminate\Support\Str;

class GroupsController extends Controller
{
    use ValidatesRequests;

    //middleware para verificar si el usuario esta logueado y es user_type admin o gerente
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function saveGroup(Request $request)
    {

        //verificar si el usuario es gestor
        if (auth()->user()->user_type->name == 'gestor' || auth()->user()->user_type->name == 'admin') {
        } else {
            return redirect('home')->with('error', 'No tienes permisos para acceder a esta página');
        }

        //validacion de los datos
        $this->validate($request, [
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:500',
        ]);
        //obtener id del usuario logueado
        $user_id = auth()->user()->id;

        //crear grupo
        $group = new Groups();
        $group->name = $request->name;
        $group->description = $request->description;
        $group->user_id = $user_id;

        //generar slug
        $group->slug = $this->generateUniqueSlug($request->name);
        $group->save();

        //añadir el usuario logueado como miembro del grupo
        $group->users()->attach($user_id);

        return redirect()->route('my-groups')->with('success', 'Grupo creado exitosamente');
    }

    public function group($slug)
    {

        //verificar si el usuario pertenece al grupo
        $group = Groups::where('slug', $slug)->firstOrFail();
        $user = auth()->user();
        if (!$group->users->contains($user->id)) {
            return redirect('home')->with('error', 'No puedes acceder a esta página');
        }
        return view('user.group', compact('group'));
    }



    public function generateUniqueSlug($name)
    {
        //generar slug
        $slug = Str::slug($name);
        //verificar si el slug ya existe
        $slugCount = Groups::where('slug', $slug)->count();
        while ($slugCount > 0) {
            //generar cadena de texto aleatoria de 5 caracteres
            $randomString = Str::random(5);
            $slug = $slug . '-' . $randomString;
            $slugCount = Groups::where('slug', $slug)->count();
        }

        return $slug;
    }
}
