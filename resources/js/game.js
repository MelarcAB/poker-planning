//on document ready
$(document).ready(function () {
    var socket = new WebSocket("ws://localhost:8090");


    //USERS LIST
    //obtener jwt del header
    var jwt = document.querySelector("meta[name='jwt']").getAttribute("content");
    var room_slug = window.location.pathname.split("/")[2];
    //users-container
    var usersContainer = $('#users-container');



    //TICKETS
    var new_tickets_container = $('#new-ticket-container');
    var new_tickets_title = $('#new-ticket-title');
    var new_tickets_description = $('#new-ticket-description');
    var b_submit_ticket = $('#b-submit-ticket');


    //EVENTS
    //submit ticket
    b_submit_ticket.click(submitNewTicket);

    //iniciar socket
    socket.onopen = function (event) {
        socket.send(JSON.stringify({
            event: 'join-room',
            jwt: jwt,
            room_slug: room_slug,
            data: {
            }
        }));
    }

    //recibir mensajes del socket
    socket.onmessage = function (event) {

        //si existe event.error, mostrar error

        var data = JSON.parse(event.data);
        console.log(data);

        //si existe event.error, mostrar error
        //sacar alerta de error
        if (data.error) {
            showError(data.error);
            console.log(data.error);
            return;
        }

        switch (data.event) {
            case "users-in-room":
                //obtener los usuarios de la sala
                renderUsersList(data.data.users);
                break;
            case 'ticket-created':
                showSuccess("Ticket añadido");
                break;
        }
    }


    function clearUsersListRender() {
        usersContainer.html('');
    }

    function renderUsersList(users) {
        console.log("rendering users list");
        console.log(users);
        clearUsersListRender();
        users.forEach(function (user) {
            //append div with class user-list-box and username and image
            let html = '<div class="user-list-box">' + '<img src="' + user.image + '" alt="">' + ' <div class="user-list-box-username">' + user.username + '</div>' + '</div>';
            usersContainer.append(html);
        });
    }




    //TICKETS
    function submitNewTicket() {
        console.log("submitting new ticket");

        socket.send(JSON.stringify({
            event: 'new-ticket',
            jwt: jwt,
            room_slug: room_slug,
            data: {
                title: new_tickets_title.val(),
                description: new_tickets_description.val()
            }
        }));
    }


    function showError(error = "Error") {
        new Noty({
            type: 'error',
            layout: 'bottomRight',
            text: error,
            timeout: 2000
        }).show();
    }

    function showSuccess(success = "Success") {
        new Noty({
            type: 'success',
            layout: 'bottomRight',
            text: success,
            timeout: 2000
        }).show();
    }



});