<div class="deck-room-container">
    <!-- Order your soul. Reduce your wants. - Augustine -->
    @foreach($cards as $card)
    <div class=" deck-room-options-box " style="">
        <img src="{{asset($card->image)}}" alt="">
        {{$card->value}}

    </div>
    @endforeach

</div>

<style>
</style>