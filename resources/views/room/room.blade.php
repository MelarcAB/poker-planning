@extends('layouts.app')

@section('content')
<input type="hidden" id="username" value="{{ Auth::user()->username }}">
<x-game.tickets-list :room="$room" />
<div class="container">
    <x-game.users-list />
    <x-game.tablero :deck="$room->group->deck" />
    <x-game.user-deck :deck="$room->group->deck" />






</div>
@vite([ 'resources/js/game.js'])

@endsection