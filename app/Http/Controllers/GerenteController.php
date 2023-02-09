<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
