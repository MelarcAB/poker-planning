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

    //constante con array estados de los tickets ( 0 por votar  ,1 votado ,2 Resultados visibles )
    const ticket_status = {
        "0": "Por votar",
        "1": "Votado",
        "2": "Resultados visibles"
    };

    //getter de ticket_status
    function getTicketStatus(status) {
        return ticket_status[status];
    }

    //get the current selected card (data-deck-card="true" and class="activo")
    function getSelectedCard() {
        let selected_card = null;
        $('[data-deck-card="true"]').each(function () {
            if ($(this).hasClass('activo')) {
                selected_card = $(this).data('deck-card-value');
            }
        });
        return selected_card;
    }

    //si la url actual tiene https, se usa wss, si no, se usa ws

    if (window.location.protocol == "https:") {
        var socket = new WebSocket("wss://" + baseURI + "/laravel-websockets");
    } else {
        var socket = new WebSocket("ws://" + baseURI + ":8090");
    }

    console.log("socket.readyState: " + socket.readyState);

    //USERS LIST
    //obtener jwt del header
    var jwt = document.querySelector("meta[name='jwt']").getAttribute("content");
    var room_slug = window.location.pathname.split("/")[2];
    //users-container
    var usersContainer = $('#users-container');
    var actual_user = $('#username');

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



    //selected_ticket will be the slug of the ticket selected
    var selected_ticket = null;
    var selected_card = null;
    //header vars
    var selectedTicket = $('#selectedTicket');
    var selectedTicketStatus = $('#selectedTicketStatus');


    //sidebar tickets
    var toggle_tickets_btn = $("#toggle-tickets-btn");
    var full_tickets_list = $('#tickets-list-full-container');

    init_room();


    //----------------------------------------------------------------------------------------------------------------------------
    //INIT----------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------
    function init_room() {
        //ocultar formulario de tickets
        clearTablero()
        refreshStatusTablero();
    }

    //funcion para limpiar el tablero
    function clearTablero() {
        $('#tablero-container').html('');
    }


    //----------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------


    //EVENTS
    //submit ticket
    //añadir nuevo ticket a la sala
    b_submit_ticket.click(submitNewTicket);
    b_cancel_ticket.click(function () {
        new_tickets_container.hide();
        b_show_tickets.show();
    });

    //mostrar formulario de tickets al pulsar el boton de añadir ticket
    b_show_tickets.click(function () {
        new_tickets_container.show();
        b_show_tickets.hide();
    });


    //mostrar/ocultar lista de tickets
    toggle_tickets_btn.click(function () {
        full_tickets_list.toggle();
    });


    //votar los tickets pulsando el boton de votar (creador de la sala)
    b_vote_ticket.click(function () {
        //validar si hay ticket seleccionado o si el ticket seleccionado ya ha sido votado
        if (selected_ticket == null || selected_ticket == "") {
            showError("No hay ticket seleccionado");
            return;
        }

        //obtener si el ticket ya ha revelado los votos
        let ticket = $('[data-ticket-slug="' + selected_ticket + '"]');
        let status = ticket.data('voted-finish');
        console.log("todos los ticket votados" + checkAllTicketsVoted());

        if (status) {
            showError("El ticket ya ha sido votado");
            return;
        }

        //validar si el ticket seleccionado ya ha revelado los votos
        if (!checkAllUsersVoted()) {
            swal({
                title: "¿Estás seguro?",
                text: "No todos los usuarios han votado el ticket " + selectedTicket.text() + ". ¿Quieres revelar los votos?",
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

    });


    //funcion para enviar los votos de los usuarios al websocket y mostrar los resultados del ticket
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


    //verificar si todos los usuarios han votado return true/false
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


    //cambiar de ticket seleccionado
    //evento a todos los elementos con data-ticket-button="true"
    $(document).on('click', '[data-ticket-button="true"]', function () {
        clickTicket($(this).data('ticket-slug'));
        refreshStatusTablero();
    });

    //cambiar de carta seleccionada
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


    //----------------------------------------------------------------------------------------------------------------------------
    //websocket-----------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------------
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
        var data = JSON.parse(event.data);
        if (data.error) {
            //sacar alerta de error
            showError(data.error);
            //    console.log(data.error);
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


            case 'votes-list':
                refreshVotes(data);

                break;
            case 'votes':
                //si data tiene el campo revelation y ademas es = true, mostrar los resultados de las votaciones con el alert
                if (data.revelation == true) {
                    showTimerVotes(data);
                } else {
                    refreshVotesFromData(data);
                }

                break;
        }


    }

    //funcion submit ticket nuevo (creador de la sala)
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

        selectedTicket.html(first_ticket.data('ticket-title'));
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

        //asignar valor al selected_card
        selected_card = valor;

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
        votes = data.data;
        refreshVotesFromData(data);
    }

    function refreshVotesFromData(data) {
        //quitar brillo a todos data-card-tablero-img="true"
        $('[data-card-tablero-img="true"]').removeClass('brillos');
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
        refreshStatusTablero();

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


    function renderTicketsList(tickets) {
        tickets_list_container.html('');
        tickets.forEach(function (ticket) {
            let html = "";
            if (ticket.visible == "true") {
                html = '<div class="custom-card-auto" data-voted-finish="true" data-ticket-title="' + ticket.title + '" data-ticket-button="true" data-ticket-slug="' + ticket.slug + '">' + '<div class="ticket-list-box-title"><i class="fa-solid fa-check"></i> ' + ticket.title + '</div>' + '</div>';
            } else {
                html = '<div class="custom-card-auto" data-voted-finish="false" data-ticket-title="' + ticket.title + '" data-ticket-button="true" data-ticket-slug="' + ticket.slug + '">' + '<div class="ticket-list-box-title">' + ticket.title + '</div>' + '</div>';
            }
            tickets_list_container.append(html);
        });
        //hide new ticket form
        new_tickets_container.hide();
        b_show_tickets.show();
        //si no hay ticket seleccionado, seleccionar el primero
        if (selected_ticket == null) {
            selectFirstTicket();
        }
        refreshStatusTablero();

    }

    function clearUsersListRender() {
        usersContainer.html('');
    }

    function renderUsersList(users) {
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
        let votado = false;
        votes.forEach(function (vote) {
            //comprobar si el ticket es el actual
            if (vote.ticket_slug == selected_ticket) {
                //comprobar si el usuario es el actual
                if (vote.user_name == username) {
                    //si es asi, seleccionar la carta añadiendo la clase brillos
                    $('[data-deck-card-value="' + vote.vote + '"]').addClass('activo');
                    votado = true;
                }
            }
        });

        refreshStatusTablero();

    }


    function refreshStatusTablero() {
        //obtener ticket seleccionado
        let selected_card = getSelectedCard();
        let ticket = $('[data-ticket-slug="' + selected_ticket + '"]');

        //obtener el html del ticket seleccionado
        let ticket_title = ticket.text();

        selectedTicket.html(ticket_title);

        //obtener el estado del ticket
        let status = ticket.data('voted-finish');
        //if true = votacion finalizada
        if (status == true) {
            selectedTicketStatus.html(getTicketStatus(2));
        } else {
            //comprobar si el usuario actual tiene una carta seleccionada en el ticket seleccionado
            if (selected_card == null) {
                selectedTicketStatus.html(getTicketStatus(0));
            } else {
                selectedTicketStatus.html(getTicketStatus(1));
            }

        }
    }

    function printUserCardTablero(user) {
        let html = '<div class="tablero-card" data-card-tablero-img="true" data-card-tablero="' + user.username + '">' + '<span>' + user.username + '</span>' + '</div>';
        $('#tablero-container').append(html);
    }




    //funcion para mostrar alerta de error
    function showError(error = "Error") {
        new Noty({
            type: 'error',
            layout: 'bottomRight',
            text: error,
            timeout: 2000
        }).show();
    }


    //funcion para mostrar alerta de success
    function showSuccess(success = "Success") {
        new Noty({
            type: 'success',
            layout: 'bottomRight',
            text: success,
            timeout: 2000
        }).show();
    }





    function showTimerVotes(data) {
        let timerInterval
        //obtener el ticket slug de data
        let ticket_slug = data.data[0].ticket_slug;
        let ticket_to_reveal = $('[data-ticket-slug="' + ticket_slug + '"]');
        Swal.fire({
            title: 'Mostrando los resultados de las votaciones del ticket ' + ticket_to_reveal.data('ticket-title'),
            html:
                'En <strong></strong> segundos<br/><br/>'
            ,
            timer: 5000,
            showConfirmButton: false,  // Esconde el botón de confirmación
            didOpen: () => {
                const content = Swal.getHtmlContainer()
                const $ = content.querySelector.bind(content)
                timerInterval = setInterval(() => {
                    content.querySelector('strong')
                        .textContent = (Swal.getTimerLeft() / 1000)
                            .toFixed(0)
                }, 100)
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                refreshVotesFromData(data);
                //añadir un tick delante del titulo del ticket
                let ticket_revelado = $('[data-ticket-slug="' + selected_ticket + '"]');
            }
        })
    }



    //function to check if all the tickets are voted
    function checkAllTicketsVoted() {
        let all_tickets_voted = true;
        $('[data-ticket-button="true"]').each(function () {
            if (!$(this).data('voted-finish')) {
                all_tickets_voted = false;
            }
        });
        return all_tickets_voted;
    }









});
