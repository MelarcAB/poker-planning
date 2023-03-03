@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class=" custom-title">Invitaciones pendientes</h3>

    <div class="custom-card-100">
        @if($invitations->count() > 0)
        <div class="custom-card-container">
            @foreach($invitations as $invitation)
            <div class="custom-card-wb-100" data-invitation-box="{{$invitation->group->slug}}">
                <div class="row justify-content-end">
                    <div class="col-md-9">
                        <!-- imagen del usuario que te ha invitado -->
                        <img class="rounded-circle" src="{{asset($invitation->sender->image)}}" style="width:25px;">
                        {{$invitation->sender->username}} te ha invitado a <b>{{ $invitation->group->name}}</b>

                    </div>
                    <div class="col-md-3 text-right">
                        <div class="d-flex">
                            <button class="lt-button-danger" data-decline-button data-group-slug="{{$invitation->group->slug}}">Rechazar</button>
                            <button class="lt-button" data-accept-button data-group-slug="{{$invitation->group->slug}}">Aceptar</button>
                            <div data-spinner-loading>
                                <i class="fas fa-spinner fa-spin" style="transform: rotate(180deg);display:inline-block"></i>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            @endforeach
        </div>


        @else
        <p class="custom-text">No tienes invitaciones pendientes</p>
        @endif



    </div>
</div>
@vite([ 'resources/js/invitations.js'])

@endsection