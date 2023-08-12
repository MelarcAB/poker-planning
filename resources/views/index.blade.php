@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="container-fluid py-5 text-center" style="background-color: #1B1F2A; color: white;">
        <div class="row">
            <div class="col-md-12">
                <img src="{{ asset('img/icon.png') }}" alt="App Icon" class="mb-3">
                <h1 class="display-5">Bienvenido a Poker Planning App</h1>
                <p class="lead">Una herramienta gratuita para una planificación eficiente y colaborativa.</p>
                <a href="{{ route('register') }}" class="btn btn-outline-light">¡Empieza a planificar ahora!</a>
            </div>
        </div>
    </div>

    <div class="container py-5" style="color: white;">
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="card bg-dark mb-4" style="height: 100%;">
                    <div class="card-body d-flex flex-column">
                        <i class="fas fa-4x fa-users mb-4 text-white"></i>
                        <h3 class="card-title">Crea Grupos</h3>
                        <p class="card-text flex-grow-1">Forma equipos y comienza a trabajar juntos en un instante. Fomenta
                            la colaboración y la toma de decisiones en grupo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card bg-dark mb-4" style="height: 100%;">
                    <div class="card-body d-flex flex-column">
                        <i class="fas fa-4x fa-layer-group mb-4 text-white"></i>
                        <h3 class="card-title">Personaliza Decks</h3>
                        <p class="card-text flex-grow-1">Adapta tu experiencia a tu manera. Personaliza los decks de
                            planning poker para que se ajusten a las necesidades de tu equipo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card bg-dark mb-3" style="height: 100%;">
                    <div class="card-body d-flex flex-column">
                        <i class="fas fa-4x fa-envelope-open-text mb-4 text-white"></i>
                        <h3 class="card-title">Invita Participantes</h3>
                        <p class="card-text flex-grow-1">Invita a otros a unirse a tu sesión de planning poker. Haz que todo
                            el equipo participe en la estimación y planificación.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4" style="color: white;">
        <h2 class="display-5 text-center mb-3"></h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <p class="text-center">
                    Aplicación desarrollada sin ánimo de lucro.
                </p>
            </div>
        </div>
    </div>
@endsection
