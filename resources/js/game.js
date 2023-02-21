//on document ready
$(document).ready(function () {
    var socket = new WebSocket("ws://localhost:8090");


    //USERS LIST
    //obtener jwt del header
    var jwt = document.querySelector("meta[name='jwt']").getAttribute("content");
    var room_slug = window.location.pathname.split("/")[2];
    //users-container
    var usersContainer = $('#users-container');

    var selected_ticket = null;
    var votes = [];


    //TICKETS
    var new_tickets_container = $('#new-ticket-container');
    var new_tickets_title = $('#new-ticket-title');
    var new_tickets_description = $('#new-ticket-description');
    var b_submit_ticket = $('#b-submit-ticket');
    var tickets_list_container = $('#tickets-list-container');
    var b_show_tickets = $('#b-show-ticket-form');
    var b_cancel_ticket = $('#b-cancel-ticket');


    init_room();


    //INIT
    function init_room() {
        //ocultar formulario de tickets
        clearTablero()
        initVars();
    }

    function initVars() {

    }



    //EVENTS
    //submit ticket
    b_submit_ticket.click(submitNewTicket);
    b_cancel_ticket.click(function () {
        new_tickets_container.hide();
        b_show_tickets.show();
    });

    b_show_tickets.click(function () {
        new_tickets_container.show();
        b_show_tickets.hide();
    });


    //evento a todos los elementos con data-ticket-button="true"
    $(document).on('click', '[data-ticket-button="true"]', function () {
        clickTicket($(this).data('ticket-slug'));
    });

    //evento al pulsar el boton de votar data-deck-card="true"
    $(document).on('click', '[data-deck-card="true"]', function () {
        clickDeckCard($(this).data('deck-card-value'));
    });

    function clickDeckCard(value) {
        if (selected_ticket == null) {
            showError("No hay ticket seleccionado");
            return;
        }
        //seleccionar carta

    }



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

            case 'update-tickets-list':
                renderTicketsList(data.data.tickets);
                break;
        }
    }

    function clickTicket(slug) {
        //recorrer todos los [data-ticket-button="true"] y quitarles la clase selected
        $('[data-ticket-button="true"]').removeClass('custom-selection');
        //añadir clase selected al elemento con data-ticket-slug = slug
        $('[data-ticket-slug="' + slug + '"]').addClass('custom-selection');
        //guardar el ticket seleccionado
        selected_ticket = slug;
    }



    function renderTicketsList(tickets) {
        tickets_list_container.html('');
        tickets.forEach(function (ticket) {
            let html = '<div class="custom-card" data-ticket-button="true" data-ticket-slug="' + ticket.slug + '">' + '<div class="ticket-list-box-title">' + ticket.title + '</div>' + '</div>';
            tickets_list_container.append(html);
        });

        //hide new ticket form
        new_tickets_container.hide();
        b_show_tickets.show();
    }

    function clearUsersListRender() {
        usersContainer.html('');
    }

    function renderUsersList(users) {
        console.log("rendering users list");
        console.log(users);
        clearUsersListRender();
        clearTablero();
        users.forEach(function (user) {
            //append div with class user-list-box and username and image
            let html = '<div class="user-list-box">' + '<img src="' + user.image + '" alt="">' + ' <div class="user-list-box-username">' + user.username + '</div>' + '</div>';
            usersContainer.append(html);
            //añadir carta al tablero
            printUserCardTablero(user);

        });
    }


    function printUserCardTablero(user) {
        //append div with class user-list-box and username and image
        /* let html = '<div class="user-list-box">' + '<img src="' + user.image + '" alt="">' + ' <div class="user-list-box-username">' + user.username + '</div>' + '</div>';
         $('#tablero-container').append(html);*/
        /* 
                <div class="tablero-card">
                <span>Melarc</span>
            </div> */

        let html = '<div class="tablero-card">' + '<span>' + user.username + '</span>' + '</div>';
        $('#tablero-container').append(html);

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


    function clearTablero() {
        //clear tablero
        $('#tablero-container').html('');
    }



});