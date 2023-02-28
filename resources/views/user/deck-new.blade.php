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
            <input type="hidden" name="slug" value="{{$deck->slug}}">

            <input type="hidden" name="slug" value="{{$deck->slug}}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group m5">
                            <label for="title">Título</label>
                            <input type="text" class="custom-input-gray-100" name="title" placeholder="Nombre del deck" value="{{$deck->title}}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group ">
                            <label for="image" style="display: block;">Imagen</label>
                            <input type="file" style="min-width: 100px;" class="custom-input-gray-100" id="image" name="image">
                        </div>
                    </div>
                </div>
                <div class="form-group m5">
                    <label for="description">Descripción</label>
                    <textarea class="custom-input-gray-100" name="description" rows="3" placeholder="Introduzca una descripción">{{$deck->description}}</textarea>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="custom-button-lt">Guardar</button>
            </div>



        </form>


    </div>
</div>
@endsection