@extends('layouts.app')
@section('content')
<div class="container">
    <div class="custom-card-100" style="margin: 15px 0;">
        <div class="row justify-content-center" style="text-align: center;">
            <h3 class="custom-title">Acceso a {{$group->name}}</h3>
            <div>
                <input type="text" id="code" name="code" class="custom-input-gray" style="width: 50%;min-width:150px;max-width:250px;display:inline-block;text-align:center" placeholder="Código">
                <i id="loading-spinner-code-access" class="fas fa-spinner fa-spin" style="transform: rotate(180deg);display:none;"></i>
            </div>
            <div>
                <button id="b-check-code" class="custom-button-lt" style="width: 50%;min-width:150px;max-width:250px">Acceder</button>
            </div>
        </div>
    </div>
</div>
@endsection