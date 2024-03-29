@extends('layouts.app')
@section('content')
<div class="container">
    <div class="custom-card-100 ">
        <div class="custom-card-header">
            <h3>My Decks</h3>
        </div>
        <div class="" style="display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start; padding: 10px; ">
            <a href="{{route('new-deck')}}" class="custom-link  m5">
                <div class="deck-container " style="">
                    <img src="">
                    <div class="deck-name"><i class="fa fa-plus"></i> Nuevo</div>
                </div>
            </a>
            @foreach($decks as $deck)
            <a href="{{route('deck',['slug'=>$deck->slug])}}" class="custom-link m5">

                <div class="deck-container" style="">
                    <img src="{{asset($deck->image)}}">
                    <div class="deck-name">{{$deck->title}}</div>
                </div>
            </a>
            @endforeach



        </div>
    </div>
    @endsection