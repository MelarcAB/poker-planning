$(document).ready(function () {

    //obtener todos los elementos data-decline-button y data-accept-button
    const decline_buttons = $('[data-decline-button]');
    const accept_buttons = $('[data-accept-button]');

    //obtener el token de la pagina
    const token = $('meta[name="csrf-token"]').attr('content');
    const bearer = $('meta[name="jwt"]').attr('content');


    //onclik de los botones
    decline_buttons.click(function (e) {
        e.preventDefault();
        let group_slug = $(this).data('group-slug');
        let action = "decline";


    });

    accept_buttons.click(function (e) {
        e.preventDefault();
        let group_slug = $(this).data('group-slug');
        let action = "accept";

    });



    function sendInvitationResponse(group_slug, action) {
        let url = '/api/manage-invitation';
        axios.post(url, {
            group_slug: group_slug,
            action: action,
        }, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Authorization': 'Bearer ' + bearer
            }
        })
            .then(function (response) {
                console.log(response);

            })
            .catch(function (error) {
                showError(error + ' ' + error.response.data.message);
            });

    }




    function showError(msg) {
        new Noty({
            type: 'error',
            layout: 'bottomRight',
            text: msg,
            timeout: 2000
        }).show();
    }

    function showSuccess(msg) {
        new Noty({
            type: 'success',
            layout: 'bottomRight',
            text: msg,
            timeout: 2000
        }).show();
    }



});