@extends('layouts.app')

@section('content')
<div class="container">

    <div class="custom-card-100" style="padding:10px 15px;text-align:center">
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <a href="{{ route('google.login') }}" class=" custom-button custom-link"><i class="fab fa-google"></i> Iniciar sesión con Google</a>
                <div class="g-signin2" data-width="300" data-height="200" data-longtitle="true">
            </form>
        </div>
    </div>
</div>



<!--
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <a href="{{ route('google.login') }}" class="btn btn-google btn-block"><i class="fab fa-google"></i> Iniciar sesión con Google</a>

                        <div class="g-signin2" data-width="300" data-height="200" data-longtitle="true">

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
-->

@endsection