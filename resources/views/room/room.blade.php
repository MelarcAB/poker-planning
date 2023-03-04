@extends('layouts.app')

@section('content')
<input type="hidden" id="username" value="{{ Auth::user()->username }}">
<div class="container">
    <x-game.users-list />
    <x-game.tickets-list :room="$room" />
    <x-game.tablero />
    <x-game.user-deck :deck="$room->group->deck" />






</div>
@vite([ 'resources/js/game.js'])

@endsection