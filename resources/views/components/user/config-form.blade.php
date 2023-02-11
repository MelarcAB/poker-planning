<div class="custom-card center-elements" style="width:80%;margin:auto">
    <form method="post" action="{{route('save-config')}}" enctype="multipart/form-data">
        @csrf
        <div class="center-elements">
            <img src="{{asset($user->image)}}" class="rounded-circle" width="200px">
        </div>

        <div class="form-group">
            <label for="username">Nombre de usuario:</label>
            <input type="text" class="custom-input-gray" id="username" value="{{$user->username}}" name="username">
        </div>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" class="custom-input-gray" id="name" disabled value=" {{$user->name}}">
        </div>
        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" class="custom-input-gray" id="email" disabled value="{{$user->email}}">
        </div>
        <div class="form-group">
            <label for="registration_date">Fecha de registro:</label>
            <input type="text" class="custom-input-gray" id="registration_date" disabled value="{{$user->created_at->format('d/m/Y')}}">
        </div>
        <div class="form-group">
            <label for="image">Imagen:</label>
            <input type="file" style="width: 250px;" class="custom-input-gray" id="image" name="image">
        </div>
        <br>
        <div class="form-group " style="text-align: center;">
            <button type="submit" class="custom-button-lt" style="width: 200px;">Guardar</button>
        </div>
    </form>
</div>