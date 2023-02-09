@extends('layouts.app')

@section('content')
<div class="container">
    <x-basic-options />
    <div class="" style="display:flex">
        <form>
            <div class="form-row">
                <div class="form-group col-md-6" style="display: inline-block;">
                    <label for="name">Nombre del grupo</label>
                    <input type="email" class="form-control" id="name" placeholder="Nombre del grupo">
                </div>
                <div class="form-group col-md-6" style="display: inline-block;">
                    <label for="inputPassword4">Descripción</label>
                    <input type="password" class="form-control" id="inputPassword4" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress">Address</label>
                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
            </div>

            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
    </div>
</div>
@endsection