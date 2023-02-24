$(document).ready(function () {
    const token = $('meta[name="csrf-token"]').attr('content');
    const bearer = $('meta[name="jwt"]').attr('content');

    var bt_search_group = $('#bt-search-group');
    var inpt_group_name = $('#group-name');


    //event click on button search group
    bt_search_group.click(searchGroup);


    //functions
    function searchGroup() {

        //axios check-code
        let url = '/api/search-group';
        axios.get(url, {
            q: inpt_group_name.val(),
        }, {
            headers: {
                'Authorization': 'Bearer ' + bearer,
                'X-CSRF-TOKEN': token,

            }
        })
            .then(function (response) {
                showSuccess(response.data.message);


            })
            .catch(function (error) {
                showError(error.response.data.message);
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