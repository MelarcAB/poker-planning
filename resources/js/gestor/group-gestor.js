//on odcument ready
$(document).ready(function () {
    var new_password_group = $('#new-password-group');
    var code = $('#code');
    var b_save_code = $('#b-save-code');
    var b_new_code = $('#b-new-code');


    //init components
    initComponents();


    //events
    b_new_code.click(function () {
        new_password_group.toggle();
    });

    b_save_code.click(submitGroupCode);

    function initComponents() {
        new_password_group.hide();

        new Noty({
            type: 'success',
            layout: 'topRight',
            text: 'Tu mensaje ha sido enviado con éxito',
            timeout: 2000
        }).show();
    }


    function submitGroupCode(e) {
        let code = $('#code').val();

        //si esta vacio	se muestra un mensaje de error
        if (code == '') {
            alert('El código no puede estar vacío');
            return;
        }

        axios.post('/api/path', {
            data: 'value'
        })
            .then(function (response) {
                console.log(response);
            })
            .catch(function (error) {
                console.log(error);
            });


    }
});