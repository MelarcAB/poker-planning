$(document).ready(function () {
    const token = $('meta[name="csrf-token"]').attr('content');
    const bearer = $('meta[name="jwt"]').attr('content');

    var bt_search_group = $('#bt-search-group');
    var inpt_group_name = $('#group-name');
    var results_container = $('#results-container');

    //event click on button search group
    bt_search_group.click(searchGroup);


    //functions
    function searchGroup() {
        let url = '/api/search-group';
        //axios get
        axios.get(url, {
            params: {
                q: inpt_group_name.val(),
            },
            headers: {
                'Authorization': 'Bearer ' + bearer,
                'X-CSRF-TOKEN': token
            }
        })
            .then(function (response) {
                showSuccess(response.data.message);
                generateResultsHtml(response.data.groups);
            })
            .catch(function (error) {
                showError(error.response.data.message);
                generateResultsHtml([]);
            });
    }


    function generateResultsHtml(groups) {
        //clear results
        results_container.html('');

        //generate html
        groups.forEach(function (group) {
            //añadir divs con los resultados
            let html = '<a href="' + group.url + '" class="custom-link ">' +
                '<div class="custom-card" style="margin-top:10px;width:100%">' +
                group.name +
                '</div>' +
                '</a>';
            results_container.append(html);

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