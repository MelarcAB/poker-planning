@extends('layouts.app')

@section('content')
<div class="container">
    <div class="custom-card-100 ">
        <div class="custom-card-header">
            @if($deck->id)
            <h3>Editar {{$deck->title}}</h3>
            @else
            <h3>Nuevo Deck</h3>
            @endif
        </div>
        <form>
        </form>


    </div>
</div>
@endsection