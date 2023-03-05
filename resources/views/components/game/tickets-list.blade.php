<div id="" class="sidebar d-flex flex-column flex-shrink-0 position-fixed end-0" style="width: 250px;height: 75vh;">
    <span id="toggle-tickets-btn" class="custom-button-lt" style="align-self: flex-end; width:auto">
        <i class="fa-sharp fa-solid fa-list"></i>
    </span>
    <div id="tickets-list-full-container" class="sidebar  custom-sidebar" style="right: 0; top: 0; bottom: 0;z-index:1;overflow-y: auto;">
        <div class="nav-link">
            @if($room->group->user->id== Auth::user()->id)
            <div id="b-show-ticket-form" class="custom-card-auto" style="text-align: center;">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> </i>Añadir
            </div>
            <div class="custom-card-auto" id="new-ticket-container" style="display: none;">
                <input class="custom-input" id="new-ticket-title" style="border:1px solid white;margin-bottom:10px;width:100%;" placeholder="Título">
                <textarea class="custom-input" id="new-ticket-description" style="border:1px solid white; width:100%" placeholder="Descripción"></textarea>
                <div>
                    <button id="b-cancel-ticket" class="custom-button-lt"><i class="fa fa-times" aria-hidden="true"></i></button>
                    <button id="b-submit-ticket" class="custom-button-lt"><i class="fa-solid fa-plus"></i></button>
                </div>
            </div>
            @endif

            <div id="tickets-list-container">
                <div class="custom-card-auto" style="text-align: center;">
                    <i class="fas fa-spinner fa-spin" style="transform: rotate(180deg);"></i>
                </div>
            </div>
        </div>
    </div>





</div>


<style>

</style>