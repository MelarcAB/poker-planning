<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//groups
use App\Models\Groups;
//status
use App\Models\RoomStatus;
//rooms
use App\Models\Room;
//str
use Illuminate\Support\Str;

class GerenteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //verifica si el usuario esta logueado y es user_type admin o gerente
        $this->middleware('auth');
        $this->middleware('isGerente');
    }


    public function formNewGroup()
    {
        return view('user.gerente.form-new-group');
    }

    public function formNewRoom($group_slug)
    {
        //obtener el grupo
        $group = Groups::where('slug', $group_slug)->first();
        //verificar si el grupo existe
        if (!$group) {
            //redireccionar con un mensaje de error
            return redirect()->route('my-groups')->with('error', 'El grupo no existe');
        }
        return view('user.gerente.new-room', compact('group'));
    }

    public function saveRoom(Request $request)
    {
        //validar los datos
        $request->validate([
            'name' => 'required | min:3 | max:50',
            'group_id' => 'required|exists:groups,id',
            'description' => 'nullable | min:3 | max:255',
        ]);
        //crear la sala
        $room = Room::create($request->all());
        //obtener el id del status con nombre "Por empezar"
        $status = RoomStatus::where('name', 'Por empezar')->first();
        //asignar el status a la sala
        $room->status()->associate($status);
        //asignar el usuario creador de la sala
        $room->user()->associate(auth()->user());

        //generar el slug
        $room->slug = $this->generateUniqueSlug($room->name);
        //guardar los cambios
        $room->save();
        //redireccionar con un mensaje de exito
        return redirect()->route('group', $room->group->slug)->with('success', 'Sala creada con exito');
    }



    public function generateUniqueSlug($name)
    {
        //generar slug
        $slug = Str::slug($name);
        //verificar si el slug ya existe
        $slugCount = Room::where('slug', $slug)->count();
        while ($slugCount > 0) {
            //generar cadena de texto aleatoria de 5 caracteres
            $randomString = Str::random(5);
            $slug = $slug . '-' . $randomString;
            $slugCount = Room::where('slug', $slug)->count();
        }

        return $slug;
    }
}
