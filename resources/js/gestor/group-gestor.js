//on odcument ready
$(document).ready(function () {
    var new_password_group = $('#new-password-group');
    var code = $('#code');
    var b_save_code = $('#b-save-code');
    var b_new_code = $('#b-new-code');
    var loading_spinner_code = $('#loading-spinner-code');



    //invitation
    var invitation_input = $('#invitation');
    var loading_spinner_invitation = $('#loading-spinner-invitation');
    var b_save_invitation = $('#b-save-invitation');
    var b_new_invitation = $('#b-new-invitation');
    var invitation_group = $('#invitation-group');



    const token = $('meta[name="csrf-token"]').attr('content');

    const bearer = $('meta[name="jwt"]').attr('content');
    //init components
    initComponents();


    //events
    b_new_code.click(function () {
        new_password_group.toggle();
        if (new_password_group.is(':visible')) {
            invitation_group.hide();
        }
    });
    b_save_code.click(submitGroupCode);
    b_new_invitation.click(function () {
        invitation_group.toggle();
        if (invitation_group.is(':visible')) {
            new_password_group.hide();
        }

    });

    b_save_invitation.click(submitInvitation);




    function submitInvitation(e) {
        let invitation = invitation_input.val();
        let username = invitation_input.val();

        if (invitation == '' || invitation == null) {
            showError('La invitación no puede estar vacía');
            return;
        }

        //obtener el slug del grupo a partir de la url
        let slug = $("#slug").val();
        let url = '/api/invitate';

        axios.post(url, {
            group_slug: slug,
            username: username,
        }, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Authorization': 'Bearer ' + bearer
            }
        })
            .then(function (response) {
                console.log(response);
                /* showSuccess(response.data.message);
                 loading_spinner_invitation.hide();
                 invitation_input.val('');
                 b_new_invitation.hide();
                 b_save_invitation.hide();
                 invitation_input.hide();
                 new_password_group.slideUp();*/
            })
            .catch(function (error) {
                loading_spinner_invitation.hide();
                showError(error.response.data.message);
            });




    }


    function initComponents() {
        new_password_group.hide();
        loading_spinner_code.hide();

        loading_spinner_invitation.hide();
        invitation_group.hide();


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

    function submitGroupCode(e) {
        let code = $('#code').val();
        //obtener el slug del grupo a partir de la url
        let slug = window.location.pathname.split('/')[2];
        let url = '/api/update-group-code';

        var allCookies = Cookies.get();
        console.log(bearer);


        //si esta vacio	se muestra un mensaje de error
        if (code == '') {
            showError('El código no puede estar vacío');
            return;
        }
        loading_spinner_code.show();
        axios.post(url, {
            code: code,
            slug: slug,
        }, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Authorization': 'Bearer ' + bearer
            }
        })
            .then(function (response) {
                console.log(response);
                showSuccess(response.data.message);
                loading_spinner_code.hide();

                new_password_group.slideUp();
            })
            .catch(function (error) {
                loading_spinner_code.hide();
                showError(error + ' ' + error.response.data.message);
            });
    }
});