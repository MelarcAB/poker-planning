@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <a href="{{ route('google.login') }}" class="custom-button">
                    <i class="fab fa-google"></i> Registrarse con Google
                </a>
            </form>
        </div>
    </div>
@endsection
