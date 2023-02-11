$(document).ready(function () {
    var loading_spinner_code_access = $('#loading-spinner-code-access');
    var b_check_code = $('#b-check-code');
    var code = $('#code');
    const token = $('meta[name="csrf-token"]').attr('content');
    const bearer = $('meta[name="bearer-token"]').attr('content');


    //on click b check code
    b_check_code.click(checkCode);




    function checkCode(e) {

        //axios check-code
        let url = '/api/check-code';
        let code = $('#code').val();
        let slug = window.location.pathname.split('/')[2];
        loading_spinner_code_access.show();
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
                loading_spinner_code_access.hide();

                new_password_group.slideUp();
            })
            .catch(function (error) {
                loading_spinner_code_access.hide();
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