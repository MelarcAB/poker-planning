@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="custom-title">Buscar grupo</h3>
    <div class="">
        <form method="post" action="{{route('save-group')}}">
            <div class="form-row custom-margins">
                <div class="form-group col-md-3" style="display: inline-block;">
                    <input type="text" class=" custom-input" style="width:100%" id="group-name" name="name" placeholder="Nombre del grupo">
                    @error('name')
                    <p class="error-custom-text">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group col-md-3" style="display: inline-block;">
                    <button class="custom-button-lt" type="button" id="bt-search-group" style="display: inline-block;min-width:50px"><i class="fas fa-search"></i></button>
                </div>
            </div>

            @csrf
        </form>
    </div>
    <div id="results-container" class="custom-card-100-invisible ">

    </div>
</div>
@vite([ 'resources/js/search-group.js'])

@endsection