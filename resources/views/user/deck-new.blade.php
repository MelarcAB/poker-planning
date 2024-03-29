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
        <form method="POST" action="{{route('save-deck')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="slug" value="{{$deck->slug}}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group ">
                            <label for="title">Título</label>
                            <input type="text" class="custom-input-gray-100" name="title" placeholder="Nombre del deck" value="{{ old('title', $deck->title) }}">
                        </div>
                    </div>
                    @if(!$deck->public)
                    <div class="col-md-6">
                        <div class="form-group ">
                            <label for="image" style="display: block;">Imagen</label>
                            <input type="file" style="min-width: 100px;" class="custom-input-gray-100" id="image" name="image">
                        </div>
                    </div>
                    @endif
                    @if($deck->image)
                    <div class="col-md-3" style="text-align:center">
                        <img src=" {{asset($deck->image)}}" alt="" style="width: auto; height: 100px;margin:auto">
                    </div>
                    @endif
                </div>
                <div class="row">
                    <div class="form-group ">
                        <label for="description">Descripción</label>
                        <textarea class="custom-input-gray-100" name="description" rows="3" placeholder="Introduzca una descripción">{{ old('description', $deck->description) }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group ">
                            <label for="cards">Cartas</label>
                            @if(!$deck->public)
                            <div style="display: flex;">
                                <input type="text" class="custom-input-gray" id="inp-card-value" placeholder="Valor de la carta">
                                <button type="button" id="b-add-card" class="custom-button-lt">Agregar</button>
                            </div>
                            @endif
                            <div id="cards-container" class="col-md-12" style="display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start; padding: 10px;">
                                @foreach($deck->cards as $card)
                                <div class="carta-deck" @if(!$deck->public)
                                    data-card="true"
                                    @endif
                                    >
                                    {{$card->value}}
                                    <input type="hidden" name="cards[]" value="{{$card->value}}">
                                </div>

                                @endforeach
                            </div>
                        </div>
                    </div>


                </div>



            </div>


            <div class=" card-footer">
                @if(!$deck->public)
                <button type="submit" class="custom-button-lt">Guardar</button>
                @endif
            </div>
        </form>
        @if(!$deck->public && $deck->user_id == Auth::user()->id)
        <form method="POST" action="{{route('delete-deck')}}">
            @method('DELETE')
            <input type="hidden" name="slug" value="{{$deck->slug}}">
            @csrf
            <button type="submit" id="b-delete-deck" class="custom-button-lt-danger"><i class="fa fa-trash"></i></button>
        </form>
        @endif
    </div>




</div>
</div>
@vite([ 'resources/js/deck-form.js'])
@endsection