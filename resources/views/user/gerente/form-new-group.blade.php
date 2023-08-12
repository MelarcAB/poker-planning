@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="custom-title">Nuevo grupo</h3>
        <div class="">
            <form method="post" action="{{ route('save-group') }}">
                <div class="form-row custom-margins">
                    <div class="form-group col-md-4" style="display: inline-block;">
                        <label for="name">Nombre del grupo</label>
                        <input type="text" class=" custom-input" id="name" name="name"
                            placeholder="Nombre del grupo" value="{{ old('name') }}">
                        @error('name')
                            <p class="error-custom-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group custom-margins">
                    <label for="inputPassword4">Descripción</label>
                    <textarea class="custom-textarea" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="error-custom-text">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group custom-margins">
                    <button type="submit" class="custom-buttom">Guardar</button>

                </div>
                @csrf
            </form>
        </div>
    </div>
@endsection
