<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    //

    public function index()
    {
        //si esta logeado redirigir a home
        if (auth()->check()) {
            return redirect()->route('home');
        }
        return view('index');
    }
}
