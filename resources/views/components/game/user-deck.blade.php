<div class="deck-room-container">
    <!-- Order your soul. Reduce your wants. - Augustine -->
    @foreach($deck->cards as $card)
    <div class=" deck-room-options-box " style="" data-deck-card="true" data-deck-card-value="{{$card->value}}">
        <img src="{{asset($card->image)}}" alt="">
        {{$card->value}}

    </div>
    @endforeach

</div>

<style>
</style>