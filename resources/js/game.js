import { get } from "jquery";

//on document ready
$(document).ready(function () {

    let baseURI = document.getElementById('base_url').value;
    //si url tiene un puerto se elimina, lo mismo para el protocolo
    baseURI = baseURI.replace("http://", "");
    baseURI = baseURI.replace("https://", "");
    baseURI = baseURI.replace(":8000/", "");
    baseURI = baseURI.replace(":8000", "");
    baseURI = baseURI.replace(":8080/", "");
    baseURI = baseURI.replace(":8080", "");


    var socket = new WebSocket("ws://" + baseURI + ":8090");


    //USERS LIST
    //obtener jwt del header
    var jwt = document.querySelector("meta[name='jwt']").getAttribute("content");
    var room_slug = window.location.pathname.split("/")[2];
    //users-container
    var usersContainer = $('#users-container');

    var actual_user = $('#username');


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
    var b_vote_ticket = $('#b-vote-ticket');


    var toggle_tickets_btn = $("#toggle-tickets-btn");
    var full_tickets_list = $('#tickets-list-full-container');




    init_room();


    //INIT
    function init_room() {
        //ocultar formulario de tickets
        clearTablero()
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

    toggle_tickets_btn.click(function () {
        // console.log("toggle");
        full_tickets_list.toggle();
    });

    b_vote_ticket.click(function () {
        if (selected_ticket == null) {
            showError("No hay ticket seleccionado");
            return;
        }

        if (!checkAllUsersVoted()) {
            swal({
                title: "¿Estás seguro?",
                text: "No todos los usuarios han votado",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        submitVotes(selected_ticket);
                    } else {
                        showSuccess("Votación cancelada");
                    }
                });
        } else {
            submitVotes(selected_ticket);

        }

        // console.log(votes)

    });

    function submitVotes(ticket_slug) {
        //cambiar ticket a visible = true al websocet
        socket.send(JSON.stringify({
            event: 'submit-votes',
            jwt: jwt,
            room_slug: room_slug,
            data: {
                ticket_slug: ticket_slug,
            }
        }));
    }


    function checkAllUsersVoted() {
        //verificar si el ticket actual está votado por todos los usuarios en la sala
        //todos los elementos data-card-tablero-img="true" deberian tener la clase brillos
        let voted = true;
        $('[data-card-tablero-img="true"]').each(function () {
            if (!$(this).hasClass('brillos')) {
                voted = false;
            }
        }
        );
        return voted;

    }


    //evento a todos los elementos con data-ticket-button="true"
    $(document).on('click', '[data-ticket-button="true"]', function () {
        clickTicket($(this).data('ticket-slug'));
    });

    //evento al pulsar el boton de votar data-deck-card="true"
    $(document).on('click', '[data-deck-card="true"]', function () {
        //verificar si tiene la clase activo, de ser asi se deselecciona y eliminamos el voto
        if ($(this).hasClass('activo')) {
            // $(this).removeClass('activo');
            quitarVoto($(this).data('deck-card-value'), $(this));
        } else {

            clickDeckCard($(this));
        }
    });



    //websocket 


    //iniciar socket
    socket.onopen = function (event) {
        socket.send(JSON.stringify({
            event: 'join-room',
            jwt: jwt,
            room_slug: room_slug,
            data: {
            }
        }));
        getVotesUser();


    }

    //recibir mensajes del socket
    socket.onmessage = function (event) {
        //si existe event.error, mostrar error
        var data = JSON.parse(event.data);
        // console.log(data);
        //si existe event.error, mostrar error
        //sacar alerta de error
        if (data.error) {
            showError(data.error);
            //    console.log(data.error);
            return;
        }

        //   console.log("RECIBIENDO EVENTO: " + data.event + "")
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

            case 'votes-list':
                refreshVotes(data);

                break;
            case 'votes':
                refreshVotesFromData(data);
                break;
        }
    }






    //FUNCIONES
    function quitarVoto(valorm, obj) {
        socket.send(JSON.stringify({
            event: 'remove-vote',
            jwt: jwt,
            room_slug: room_slug,
            ticket_slug: selected_ticket,
            data: {
            }
        }));
        getVotes();
        $(obj).removeClass('activo');
    }


    function checkUserVotation(username, ticket_slug) {
        let voted = false;
        votes.forEach(function (vote) {
            if (vote.ticket == ticket_slug && vote.username == username) {
                voted = true;
            }
        });
        return voted;
    }


    function selectFirstTicket() {
        let first_ticket = $('[data-ticket-button="true"]').first();
        clickTicket(first_ticket.data('ticket-slug'));
    }


    function clickDeckCard(e) {
        if (selected_ticket == null) {
            showError("No hay ticket seleccionado");
            return;
        }
        let valor = $(e).data('deck-card-value');

        //añadir brillos al elemento seleccionado
        $('[data-deck-card="true"]').removeClass('activo');
        $(e).addClass('activo');
        //enviar voto
        socket.send(JSON.stringify({
            event: 'vote',
            jwt: jwt,
            room_slug: room_slug,
            data: {
                ticket_slug: selected_ticket,
                value: valor,
                username: username
            }
        }));
        getVotes();

    }




    function refreshVotes(data) {
        //recorrer data.data 
        votes = data.data;
        refreshVotesFromData(data);
    }

    function refreshVotesFromData(data) {
        //quitar brillo a todos data-card-tablero-img="true"
        $('[data-card-tablero-img="true"]').removeClass('brillos');
        // console.log("refreshVotesFromData")
        votes = data.data;
        votes.forEach(function (vote) {
            //comprobar si la ticket_slug es = a ticket seleccionado
            if (vote.ticket_slug == selected_ticket) {
                //console.log("Ticket: " + vote.ticket_slug + " - Usuario: " + vote.user_name + " - Valor: " + vote.vote)
                //si es asi, añadir la clase brillos al elemento con data-card-tablero = user
                let carta = $('[data-card-tablero="' + vote.user_name + '"]')
                carta.addClass('brillos');
                if (vote.visible == "true") {
                    //  console.log("visible")
                    //mostrar valor de vote.vote
                    carta.html(vote.vote);
                } else {
                    //mostrar vote.username
                    carta.html(vote.user_name);
                }
            }

        });
        selectDeckCard();
    }

    function refreshVotesFromVotes() {
        //quitar brillo a todos data-card-tablero-img="true"
        $('[data-card-tablero-img="true"]').removeClass('brillos');

        votes.forEach(function (vote) {
            //comprobar si la ticket_slug es = a ticket seleccionado
            if (vote.ticket_slug == selected_ticket) {
                // console.log("Ticket: " + vote.ticket_slug + " - Usuario: " + vote.user_name + " - Valor: " + vote.vote)
                //si es asi, añadir la clase brillos al elemento con data-card-tablero = user
                $('[data-card-tablero="' + vote.user_name + '"]').addClass('brillos');
                if (vote.visible == "true") {
                    //mostrar "vote.vote" en el elemento con data-card-tablero-img="true" y data-card-tablero="vote.user_name"
                    $('[data-card-tablero-img="true"][data-card-tablero="' + vote.user_name + '"]').html(vote.vote);

                } else {
                    //mostrar username
                    $('[data-card-tablero-img="true"][data-card-tablero="' + vote.user_name + '"]').html(vote.user_name);
                }
            }


        });
    }




    function clickTicket(slug) {
        //recorrer todos los [data-ticket-button="true"] y quitarles la clase selected
        $('[data-ticket-button="true"]').removeClass('activo');
        //añadir clase selected al elemento con data-ticket-slug = slug
        $('[data-ticket-slug="' + slug + '"]').addClass('activo');
        //guardar el ticket seleccionado
        selected_ticket = slug;
        $('[data-card-tablero-img="true"]').removeClass('brillos');
        //refrescar cartas seleccionadas y voto del ticket seleccionado
        //primero deseleccionar todas las cartas
        $('[data-deck-card="true"]').removeClass('activo');
        votes.forEach(function (vote) {
            //comprobar si la ticket_slug es = a ticket seleccionado
            if (vote.ticket_slug == selected_ticket) {
                //  console.log("Ticket: " + vote.ticket_slug + " - Usuario: " + vote.user_name + " - Valor: " + vote.vote)
                //si es asi, añadir la clase brillos al elemento con data-card-tablero = user
                $('[data-card-tablero="' + vote.user_name + '"]').addClass('brillos');
                $('[data-card-tablero-img="true"][data-card-tablero="' + vote.user_name + '"]').html(vote.user_name);
                if (vote.visible == "true") {
                    $('[data-card-tablero-img="true"][data-card-tablero="' + vote.user_name + '"]').html(vote.vote);
                }
            }

        });
        selectDeckCard();

    }

    //funcion para llamar los votos de la sala
    function getVotes() {
        socket.send(JSON.stringify({
            event: 'get-votes',
            jwt: jwt,
            room_slug: room_slug,
            data: {
            }
        }));
    }
    //funcion para llamar los votos de la sala
    function getVotesUser() {
        socket.send(JSON.stringify({
            event: 'get-votes-user',
            jwt: jwt,
            room_slug: room_slug,
            data: {
            }
        }));
    }


    function checkSelectedTableroCards() {
        //obtener el div de la carta (data-card-tablero = username)
        let card = $('[data-card-tablero="' + username + '"]');
        //añadir clase brillos al div de la carta
    }


    function renderTicketsList(tickets) {
        tickets_list_container.html('');
        tickets.forEach(function (ticket) {
            let html = '<div class="custom-card-auto" data-ticket-button="true" data-ticket-slug="' + ticket.slug + '">' + '<div class="ticket-list-box-title">' + ticket.title + '</div>' + '</div>';
            tickets_list_container.append(html);
        });
        //hide new ticket form
        new_tickets_container.hide();
        b_show_tickets.show();
        //si no hay ticket seleccionado, seleccionar el primero
        if (selected_ticket == null) {
            selectFirstTicket();
        }
    }

    function clearUsersListRender() {
        usersContainer.html('');
    }

    function renderUsersList(users) {
        //console.log("rendering users list");
        //console.log(users);
        clearUsersListRender();
        clearTablero();
        users.forEach(function (user) {
            //si user.image contiene localhost/, sustituirlo por ""
            if (user.image.includes("localhost/")) {
                user.image = user.image.replace("localhost/", "");
            }
            //append div with class user-list-box and username and image
            //  let html = '<div class="user-list-box">' + '<img src="' + user.image + '" alt="">' + ' <div class="user-list-box-username">' + user.username + '</div>' + '</div>';
            //  usersContainer.append(html);
            //añadir carta al tablero
            printUserCardTablero(user);
            refreshVotesFromVotes();
        });
    }


    function selectDeckCard() {
        //a partir de votes miraremos si el ticket actual tiene un voto de este usuario y si es asi, seleccionaremos la carta
        //recorrer votes
        let username = actual_user.val();
        votes.forEach(function (vote) {
            //comprobar si el ticket es el actual
            if (vote.ticket_slug == selected_ticket) {
                //comprobar si el usuario es el actual
                if (vote.user_name == username) {
                    //si es asi, seleccionar la carta añadiendo la clase brillos
                    $('[data-deck-card-value="' + vote.vote + '"]').addClass('activo');
                }
            }
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

        let html = '<div class="tablero-card" data-card-tablero-img="true" data-card-tablero="' + user.username + '">' + '<span>' + user.username + '</span>' + '</div>';
        $('#tablero-container').append(html);

    }



    //TICKETS
    function submitNewTicket() {

        //validar titulo y descripcion
        if (new_tickets_title.val().trim() == "") {
            showError("Verifica los campos antes de añadir un ticket");
            return;
        }

        socket.send(JSON.stringify({
            event: 'new-ticket',
            jwt: jwt,
            room_slug: room_slug,
            data: {
                title: new_tickets_title.val().trim(),
                description: new_tickets_description.val().trim()
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



    function renderTablero() {

    }

});