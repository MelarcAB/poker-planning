$(document).ready(function () {
    var loading_spinner_code = $('#loading-spinner-code-access');
    var b_check_code = $('#b-check-code');
    var code = $('#code');
    const token = $('meta[name="csrf-token"]').attr('content');


    //on click b check code
    b_check_code.click(checkCode);




    function checkCode(e) {
        alert('asdasd')
    }


});