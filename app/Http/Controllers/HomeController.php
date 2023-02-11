<?php

namespace App\Http\Controllers;

//auth
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        return view('user.home');
    }

    public function my_groups()
    {

        //obtener usuario logeado
        $user = auth()->user();

        return view('user.my-groups', compact('user'));
    }

    public function config()
    {
        return view('user.config');
    }

    public function saveConfig(Request $request)
    {
        try {
            //obtener usuario logeado
            $user = Auth::user();

            //validar datos
            $request->validate([
                'username' => 'required | min:3 | max:20',
            ]);

            //actualizar datos
            $user->username = $request->username;
            $user->save();

            return redirect()->route('config')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('config')->with('error', $e->getMessage());
        }
    }
}
