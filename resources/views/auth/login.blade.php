@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <a href="{{ route('google.login') }}" class="custom-button">
                    <i class="fab fa-google"></i> Iniciar sesión con Google
                </a>
            </form>
        </div>
    </div>
@endsection
