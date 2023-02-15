<div class="basic-options-container">
    <!-- Order your soul. Reduce your wants. - Augustine -->
    @foreach($cards as $card)
    <div class=" basic-options-box ">
        <img src="{{asset($card->image)}}" alt="">
        {{$card->value}}

    </div>
    @endforeach

</div>