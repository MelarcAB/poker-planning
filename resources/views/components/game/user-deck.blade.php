<div class="fixed-bottom  d-flex overflow-auto bg-color-primary" style="padding: 10px 0;">
    <div class="btn-group mx-auto" role="group">

        @foreach($deck->cards as $card)
        <div class="card-deck" style="" data-deck-card="true" data-deck-card-value="{{$card->value}}">
            <button type="button" class="btn btn-secondary" style="font-size:1.2em;"> {{$card->value}}</button>
        </div>
        @endforeach
    </div>
</div>

<style>

</style>

<?php //ANTIGUO -> no adaptado a mobil
/*
<div class="deck-room-container" style="display: none;">
    @foreach($deck->cards as $card)
    <div class=" deck-room-options-box " style="" data-deck-card="true" data-deck-card-value="{{$card->value}}">
        <img src="{{asset($card->image)}}" alt="">
        {{$card->value}}

    </div>
    @endforeach

</div>
*/
?>