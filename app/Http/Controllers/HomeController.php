<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
//deck
use App\Models\Deck;
//user
use App\Models\User;
//auth
use Illuminate\Support\Facades\Auth;

//Validator
use Illuminate\Support\Facades\Validator;

//groups
use App\Models\Groups;

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
            //username solo permite letras, numeros, guiones y guiones bajos
            $request->validate([
                'username' => 'required | min:3 | max:20 | regex:/^[a-zA-Z0-9-_]+$/',
                'image' => 'image | mimes:jpeg,png,jpg,gif,svg | max:2048'
            ]);

            //verificar si el nombre de usuario ya existe
            $userExists = User::where('username', $request->username)->first();
            if ($userExists && $userExists->id != $user->id) {
                return redirect()->route('config')->with('error', 'El nombre de usuario ya existe');
            }


            //actualizar datos
            $user->username = $request->username;
            $user->save();

            //mirar si se ha cambiado la imagen
            if ($request->hasFile('image')) {
                //obtener la imagen
                $image = $request->file('image');
                $filename = $image->getClientOriginalName();
                //guardar  la imagen en el servidor con el nombre de usuario.extension
                $image->move(public_path('img/users'), $user->username . '.' . $image->getClientOriginalExtension());

                //actualizar la ruta de la imagen en la base de datos
                $user->image = 'img/users/' . $user->username . '.' . $image->getClientOriginalExtension();
                $user->save();
            }

            return redirect()->route('config')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('config')->with('error', $e->getMessage());
        }
    }

    public function my_decks()
    {
        //obtener deck del usuario logeado + deck publicos
        $user = auth()->user();
        //ordenar por id descendente
        $decks = Deck::where('user_id', $user->id)->orWhere('public', 1)->orderBy('id', 'desc')->get();
        return view('user.my-decks', compact('decks'));
    }
    public function search_group()
    {
        //obtener deck del usuario logeado + deck publicos
        return view('user.search-group');
    }


    public function save_deck(Request $request)
    {
        try {
            //obtener usuario logeado
            $user = Auth::user();

            //verificar si tiene slug
            if ($request->slug != '') {
                //buscar deck por titulo
                $deck = Deck::where('slug', $request->slug)->first();
                if (!$deck) {
                    return redirect()->route('my-decks')->with('error', 'Deck no encontrado');
                }
                //verificar si el deck pertenece al usuario
                if ($deck->user_id != $user->id) {
                    return redirect()->route('my-decks')->with('error', 'No tienes permisos para editar este deck');
                }
                //veroificar si el deck es publico
                if ($deck->public) {
                    return redirect()->route('my-decks')->with('error', 'No puedes editar un deck público');
                }
            } else {
                $deck = new Deck();
                //verificar si el deck ya existe
                $deckExists = Deck::where('title', $request->title)->first();
                if ($deckExists) {
                    return redirect()->route('new-deck')->with('error', 'Ya existe un deck con ese título')->withInput();
                }
                //generar slug
                $deck->slug = $this->generateUniqueSlug($request->title);
            }


            //validar datos

            $validator = Validator::make($request->all(), [
                'title' => 'required | min:3 | max:20',
                'description' => 'required | min:3 | max:100',
                'image' => 'image | mimes:jpeg,png,jpg,gif,svg | max:2048'
            ]);
            if ($validator->fails()) {

                //validar si tiene ID, si es así redirigir a editar
                if ($deck->id > 0) {
                    return redirect()->route('deck', $deck->slug)->with('error', $validator->errors()->first())->withInput();
                } else {
                    return redirect()->route('new-deck')->with('error', $validator->errors()->first())->withInput();
                }
            }

            //guardar deck
            $deck->title = $request->title;
            $deck->description = $request->description;
            $deck->public = false;
            $deck->user_id = $user->id;




            //comprobar si tiene imagen
            if ($request->hasFile('image')) {
                //obtener la imagen
                $image = $request->file('image');
                $filename = $image->getClientOriginalName();
                //guardar  la imagen con el slug del deck y en su formato original. Se guarda en la carpeta public / img / decks / username / slug.extension
                $image->move(public_path('img/decks/' . $user->username), $deck->slug . '.' . $image->getClientOriginalExtension());
                //actualizar la ruta de la imagen en la base de datos
                $deck->image = 'img/decks/' . $user->username . '/' . $deck->slug . '.' . $image->getClientOriginalExtension();
            }



            $deck->save();

            //eliminar todas las cartas del deck
            $deck->cards()->delete();
            //crer nuevas cartas
            //si hay cards
            if ($request->cards) {
                //recorrer cards
                foreach ($request->cards as $card) {
                    //crear nueva carta
                    if ($card != '') {
                        $deck->cards()->create([
                            'value' => $card
                        ]);
                    }
                }
            }






            return redirect()->route('deck', $deck->slug)->with('success', 'Deck guardado');
        } catch (\Exception $e) {
            return redirect()->route('new-deck')->with('error', $e->getMessage())->withInput();
        }
    }

    public function new_deck($slug = 0)
    {
        if ($slug == 0) {
            $deck = new Deck();
        } else {
            //buscar deck por titulo
            $deck = Deck::where('slug', $slug)->first();
            if (!$deck) {
                return redirect()->route('my-decks')->with('error', 'Deck no encontrado');
            }
        }


        return view('user.deck-new', compact('deck'));
    }

    function generateUniqueSlug($title)
    {
        $slug = str_slug($title);
        $count = Deck::where('title', 'LIKE', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }



    //delete_deck - method delete (post)
    public function delete_deck(Request $request)
    {
        try {
            //obtener usuario logeado
            $user = Auth::user();
            //buscar deck por titulo
            $deck = Deck::where('slug', $request->slug)->first();
            if (!$deck) {
                return redirect()->route('my-decks')->with('error', 'Deck no encontrado');
            }
            //verificar si el deck pertenece al usuario
            if ($deck->user_id != $user->id) {
                return redirect()->route('my-decks')->with('error', 'No tienes permisos para eliminar este deck');
            }
            //veroificar si el deck es publico
            if ($deck->public) {
                return redirect()->route('my-decks')->with('error', 'No puedes eliminar un deck público');
            }

            //reasignar el deck Default a los grupos que usen el deck a eliminar
            $groups = Groups::where('deck_id', $deck->id)->get();
            //obtener id del deck default (1)
            $default_deck = Deck::where('title', 'Default')->first();

            foreach ($groups as $group) {
                $group->deck_id = $default_deck->id;
                $group->save();
            }
            //eliminar deck soft delete
            //eliminar primero las cartas
            $deck->cards()->delete();
            $deck->delete();

            return redirect()->route('my-decks')->with('success', 'Deck eliminado');
        } catch (\Exception $e) {
            return redirect()->route('my-decks')->with('error', $e->getMessage());
        }
    }
}
