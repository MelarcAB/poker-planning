<div class="custom-card center-elements" style="width:80%;margin:auto">
    <form method="post" action="{{route('save-room')}}" enctype="multipart/form-data">
        <h4 class="custom-title">Nueva sala para {{$group->name}}</h4>
        @csrf

        <div class="form-group">
            <label for="name">Nombre de la sala:</label>
            <input type="text" class="custom-input-gray" id="name" value="" name="name">
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>

            <textarea class="custom-input-gray" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-group " style="text-align: center;">
            <button type="submit" class="custom-button-lt" style="width: 200px;">Guardar</button>
        </div>
        <input type="hidden" name="group_id" value="{{$group->id}}">
    </form>
</div>