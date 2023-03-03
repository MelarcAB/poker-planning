$(document).ready(function () {

    //obtener todos los elementos data-decline-button y data-accept-button
    const decline_buttons = $('[data-decline-button]');
    const accept_buttons = $('[data-accept-button]');

    //obtener el token de la pagina
    const token = $('meta[name="csrf-token"]').attr('content');
    const bearer = $('meta[name="jwt"]').attr('content');




    initPage();





    function initPage() {
        //ocultar data-spinner-loading
        $('[data-spinner-loading]').hide();
    }


    //onclik de los botones
    decline_buttons.click(function (e) {
        e.preventDefault();
        let group_slug = $(this).data('group-slug');
        let action = "reject";
        sendInvitationResponse(group_slug, action, e);
        $(this).hide();
        $(this).siblings().hide();
        $(this).siblings('[data-spinner-loading]').show();
    });

    accept_buttons.click(function (e) {
        e.preventDefault();
        let group_slug = $(this).data('group-slug');
        let action = "accept";
        sendInvitationResponse(group_slug, action, e);
        //ocultar boton aceptar y rechazar, mostrar spinner
        $(this).hide();
        $(this).siblings().hide();
        $(this).siblings('[data-spinner-loading]').show();
    });





    function sendInvitationResponse(group_slug, action, e) {
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
                let msg = response.data.message;
                let group_slug = response.data.group_slug;
                showSuccess(msg);
                //eliminar EL PRIMER  elemento que tenga data-invitat(ion-box = group_slug
                $(`[data-invitation-box="${group_slug}"]`).hide();
            })
            .catch(function (error) {
                showError(error.response.data.message);
                console.log($(e.target))
                $(`[data-invitation-box="${group_slug}"]`).hide();
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