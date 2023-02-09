@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Registro') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="flex justify-center mt-4">
                            <a href="{{ route('google.login') }}">
                                <img src="https://developers.google.com/identity/images/btn_google_signin_light_normal_web.png" alt="Iniciar sesión con Google">
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection