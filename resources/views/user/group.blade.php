@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class=" custom-title">Grupo {{$group->name}}</h3>
    @if(Auth::user()->isGestor() && Auth::user()->id == $group->user_id )
    <x-gestor-group-options :group="$group" />
    @endif

    <?php
    /*  foreach ($_COOKIE as $cookieName => $cookieValue) {
        echo "$cookieName: $cookieValue\n";
    } */

    ?>
    <div class="custom-card-100">
        <span class="floating-right"><i class="fa-solid fa-users"></i>{{$group->users()->count()}}</span>
        <div>
            <label>Deck</label>
            <select class="custom-input">
                @foreach(Auth::user()->decks_disponibles() as $deck)
                <option class="custom-option" value="{{$deck->id}}" @if($deck->id == $group->deck_id)
                    selected
                    @endif
                    >{{$deck->title}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <p class="custom-text">{{$group->description}}</p>
        </div>
        <div class="custom-card-100">
            <div class="row justify-content-center">
                <h3 class="custom-title">Salas por empezar</h3>
                <div class="custom-card-container">
                    @foreach($group->rooms->where('room_status_id','=','1') as $room)
                    <div class="custom-card-wb">
                        <a class="custom-link" style="color:white" href="{{route('group.room',['group_slug'=>$group->slug,'room_slug'=>$room->slug])}}">
                            {{ $room->name }}
                            <span class="floating-right">{{$room->status->name}} <i class="fa fa-circle"></i></span>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>


            <div class="custom-card-100">
                <h4 class="custom-title">Miembros</h4>
                <div class="custom-card-container">
                    @foreach($group->users as $user)
                    <div class="custom-card-100">
                        <a class="custom-link">
                            <div class="custom-card-secondary">
                                <div style="display: inline-block;vertical-align:top;margin-right:5px"><img class="rounded-circle" src="{{asset($user->image)}}" style="width:50px;"></div>
                                <div style="display: inline-block">
                                    {{ $user->name }}
                                    <p class="custom-text">{{$user->email}}</p>
                                </div>

                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="row justify-content-center">
                <h3 class="custom-title">Salas terminadas</h3>
                <div class="custom-card-container">
                    @foreach($group->rooms->whereIn('room_status_id',[2,3]) as $room)
                    <div class="custom-card-wb">
                        <a class="custom-link" style="color:white" href="{{route('group.room',['group_slug'=>$group->slug,'room_slug'=>$room->slug])}}">
                            {{ $room->name }}
                            <span class="floating-right">{{$room->status->name}} <i class="fa fa-circle"></i></span>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>


    </div>
    @endsection