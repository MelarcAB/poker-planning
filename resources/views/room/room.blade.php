@extends('layouts.app')

@section('content')
<input type="hidden" id="username" value="{{ Auth::user()->username }}">
<div class="container">
    <x-game.users-list />
</div>
@vite([ 'resources/js/game.js'])

@endsection