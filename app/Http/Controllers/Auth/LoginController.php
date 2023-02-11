<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        // Obtener los datos del usuario desde Google
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Verificar si el usuario ya existe en la base de datos por email
        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            // Si el usuario ya existe, actualizar su google_id y iniciar sesión
            $user->provider_id = $googleUser->id;
            $user->save();
            Auth::login($user);
        } else {
            // Si el usuario no existe, crear un nuevo usuario con los datos de Google y iniciar sesión
            $user = new User();
            $user->name = $googleUser->name;
            // $user->nickname = Helper::validateNickname($googleUser->name);
            $user->email = $googleUser->email;
            $user->provider_id = $googleUser->id;
            $user->password = bcrypt(bin2hex(random_bytes(4)));

            //Generar un nickname único
            $user->username = $this->generateUniqueUsername($user->email);

            //asignar tipo de usuario
            $user->user_type_id = 1;

            //asignar avatar por defecto
            $user->image = 'img/default_user.jpg';
            $user->save();
            Auth::login($user);
        }
        //generar token de sesion
        $accessToken = auth()->user()->createToken('Token Name')->accessToken;
        //asignar token de sesion a la cookie 1 semana, permitir en http y https
        $cookie = cookie('BEARER_TOKEN', $accessToken, 60 * 24 * 7);
        //actualizar api_token del usuario
        $user->api_token = $cookie->getValue();
        $user->save();

        return redirect()->route('home')->cookie($cookie);
    }


    public function generateUniqueUsername($email = "")
    {
        if ($email == "") return false;
        // Remover todo lo que no sea letras o números del email

        //obtener todo el texto antes del @
        $newUsername = explode("@", $email)[0];
        $newUsername = preg_replace("/[^a-zA-Z0-9]+/", "", $newUsername);

        // Verificar si el nombre de usuario ya existe en la base de datos
        $check = User::where('username', $newUsername)->first();


        // Si el nombre de usuario ya existe, agregar un número al final hasta que sea único
        $i = 1;
        while ($check) {
            $newUsername = $newUsername . $i;
            $check = User::where('username', $newUsername)->first();
            $i++;
        }

        return $newUsername;
    }
}
