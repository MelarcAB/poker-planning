@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class=" custom-title">Grupo {{$group->name}}</h3>
    @if(Auth::user()->isGestor() && Auth::user()->id == $group->user_id )
    <x-gestor-group-options />
    @endif

    <div class="custom-card-100">
        <span class="floating-right"><i class="fa-solid fa-users"></i>{{$group->users()->count()}}</span>
        <div>
            <p class="custom-text">{{$group->description}}</p>
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
    </div>


</div>
@endsection