<div class="sidebar d-flex flex-column flex-shrink-0 bg-light position-fixed end-0" style="width: 250px;height: 100vh;">
    <div class="sidebar  custom-sidebar" style="right: 0; top: 0; bottom: 0; background:rgba(255, 255, 255, .1);;z-index:1;">
        <div class="nav-link">
            @if($room->group->user->id== Auth::user()->id)
            <div id="b-show-ticket-form" class="custom-card-auto" style="text-align: center;">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> </i>Añadir
            </div>
            <div class="custom-card-auto" id="new-ticket-container" style="display: none;">
                <input class="custom-input" id="new-ticket-title" style="border:1px solid white;margin-bottom:10px;width:100%;" placeholder="Título">
                <textarea class="custom-input" id="new-ticket-description" style="border:1px solid white; width:100%" placeholder="Descripción"></textarea>
                <div>
                    <button id="b-submit-ticket" class="custom-button-lt"><i class="fa-solid fa-plus"></i></button>
                    <button id="b-cancel-ticket" class="custom-button-lt"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
            </div>
            @endif

            <div id="tickets-list-container">
                <div class="custom-card">
                </div>
            </div>
        </div>
    </div>





</div>


<style>

</style>