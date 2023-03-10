<div>
    <div class="basic-options-container">
        <div class="basic-options-box">
            <a href="{{route('form-new-room',['group_slug'=>$group->slug])}}" class="custom-link" style="color: white;">
                Nueva sala
            </a>
        </div>
        <div class="basic-options-box" id="b-new-code">
            Código
        </div>
        <div class="basic-options-box" id="b-new-invitation">
            Invitar
        </div>
    </div>
    <x-gestor.new-password-form :group="$group" />
    <x-group.invitation-form :group="$group" :user="Auth::user()" />
</div>