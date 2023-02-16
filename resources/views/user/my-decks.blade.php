@extends('layouts.app')

@section('content')
<div class="container">
    <div class="custom-card-100 ">
        <div class="custom-card-header">
            <h3>My Decks</h3>
        </div>
        <div class="">
            @foreach($decks as $deck)
            <div class="deck-container" style="">
                <img src="{{asset($deck->image)}}">
                <div class="deck-name">{{$deck->title}}</div>
            </div>

            @endforeach

        </div>
    </div>
    @endsection