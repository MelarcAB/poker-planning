<div class="sidebar position-fixed custom-sidebar" style="right: 0; top: 0; bottom: 0; background:rgba(255, 255, 255, .1);;z-index:1">
    <div class="nav-link p-3" style="margin-top: 80px;">
        @if($room->group->user->id== Auth::user()->id)
        <div id="b-show-ticket-form" class="custom-card" style="text-align: center;">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> </i>Añadir
        </div>
        <div class="custom-card" id="new-ticket-container" style="display: none;">
            <input class="custom-input" id="new-ticket-title" style="border:1px solid white;margin-bottom:10px" placeholder="Título">
            <textarea class="custom-input" id="new-ticket-description" style="border:1px solid white; width:100%" placeholder="Descripción"></textarea>
            <div>
                <button id="b-submit-ticket" class="custom-button-lt">Añadir</button>
                <button id="b-cancel-ticket" class="custom-button-lt">Cancelar</button>
            </div>
        </div>
        @endif

        <div id="tickets-list-container">
            <div class="custom-card">
            </div>
        </div>
    </div>
</div>