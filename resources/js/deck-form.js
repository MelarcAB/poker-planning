$(document).ready(function () {

    const b_add_card = $('#b-add-card');
    const inp_card_value = $('#inp-card-value');
    const cards_container = $('#cards-container');



    b_add_card.click(addCard);

    initListenerCards();


    function initListenerCards() {
        //todos los elementos con data-card=true onclick
        $('[data-card="true"]').click(function () {
            $(this).remove();
        });
    }



    function addCard(e) {
        e.preventDefault();
        let card_value = inp_card_value.val();
        if (card_value === '') {
            return;
        }
        let card = `<div class="carta-deck" data-card="true">
                            ${card_value}
                            <input type="hidden" name="cards[]" value="${card_value}">
                        </div>`;
        cards_container.append(card);
        inp_card_value.val('');
        initListenerCards();

    }


});