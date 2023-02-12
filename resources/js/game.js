//on document ready
$(document).ready(function () {
    var socket = new WebSocket("ws://localhost:8090");

    //obtener jwt del header
    var jwt = document.querySelector("meta[name='jwt']").getAttribute("content");
    var room_slug = window.location.pathname.split("/")[2];
    //users-container
    var usersContainer = $('#users-container');

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
        var data = JSON.parse(event.data);
        console.log(data);
        //obtener los usuarios de la sala
        if (data.event == 'users-in-room') {
            console.log("Loading users list");
            renderUsersList(data.data.users);
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
            //append div with class user-list-box and username
            usersContainer.append('<div class="user-list-box">' + user.username + '</div>');
        });
    }

});