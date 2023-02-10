@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h3 class="custom-title">Mis grupos</h3>"
        <div class="custom-card-container">
            @foreach($user->groups as $group)
            <a class="custom-link" href="{{route('group',['slug'=>$group->slug])}}">
                <div class="custom-card">
                    {{ $group->name }}
                    <span class="floating-right"><i class="fa-solid fa-users"></i>{{$group->users->count()}}</span>
                    <p class="custom-text">{{$group->description}}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @if($user->isGestor())
    <h3 class="custom-title">Grupos creados por ti</h3>
    <div class="custom-card-container">
        @foreach($user->groups_created as $group)
        <a class="custom-link" href="{{route('group',['slug'=>$group->slug])}}">
            <div class="custom-card">
                {{ $group->name }}
                <span class="floating-right"><i class="fa-solid fa-users"></i>{{$group->users->count()}}</span>
                <p class="custom-text">{{$group->description}}</p>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection