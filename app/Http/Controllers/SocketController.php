<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
//broadcast
use Ratchet\Wamp\WampServerInterface;
//jwt
use \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
//user
use App\Models\User;
//group
use App\Models\Groups;
//room
use App\Models\Room;
//entry
use App\Models\Tickets;

class SocketController extends Controller implements MessageComponentInterface
{
    protected $clients;
    private $connections = [];
    private $rooms = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo ">Se ha iniciado el servidor de sockets\n";
    }

    function onOpen(ConnectionInterface $conn)
    {
        $this->connections[$conn->resourceId] = compact('conn') + ['jwt' => null];
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {

        $disconnectedId = $conn->resourceId;
        unset($this->connections[$disconnectedId]);
        foreach ($this->connections as &$connection) {
            $connection['conn']->send(json_encode([
                'offline_user' => $disconnectedId,
                'from_user_id' => 'server control',
                'from_resource_id' => null
            ]));
        }

        //quitar de rooms el usuario del slug
        foreach ($this->rooms as $slug => $room) {
            if (isset($room[$disconnectedId])) {
                unset($this->rooms[$slug][$disconnectedId]);
            }
        }

        //borrar rooms vacios
        foreach ($this->rooms as $slug => $room) {
            if (empty($room)) {
                unset($this->rooms[$slug]);
            }
        }

        $this->clients->detach($conn);
        echo "Se ha desconectado el usuario con id $disconnectedId\n";

        //borrar de connections el usuario
        $this->sendUsersInRoom($slug);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $userId = $this->connections[$conn->resourceId]['jwt'];
        //obtener nombre usuario
        $user = User::where('api_token', $userId)->first();
        $username = $user->username;
        echo "An error has occurred with user $username: {$e->getMessage()}\n";
        unset($this->connections[$conn->resourceId]);
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $conn The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $conn, $msg)
    {
        $data = json_decode($msg);
        echo 'recibe evento: ' . $data->event . "\n";
        switch ($data->event) {
            case 'join-room':
                //print "User {$data->jwt} joined room {$data->room_slug}\n";
                $this->connections[$conn->resourceId]['jwt'] = $data->jwt;
                $this->onJoinRoom($conn, $data);
                break;
            case 'new-ticket':
                echo "entra case new-ticket \n";

                $this->onNewTicket($conn, $data);
                break;
        }
    }

    public function onNewTicket(ConnectionInterface $conn, $data)
    {

        $user = User::where('api_token', $data->jwt)->first();
        $room_slug = $data->room_slug;

        //obtener el objeto de la sala
        $room = Room::where('slug', $room_slug)->first();

        //verificar que el user es el creador de la sala (group)
        $group = Groups::where('id', $room->group_id)->first();
        if ($group->user_id != $user->id) {
            $response =  json_encode([
                'error' => 'No eres el creador de la sala',
            ]);
            $conn->send($response);
            return;
        }

        //verificar que el ticket no existe por titulo
        $tickets = $room->tickets;
        if (count($tickets) == 0 || $tickets == null) {
            $tickets = [];
        }
        foreach ($tickets as $ticket) {
            if ($ticket->title == $data->data->title) {
                $response = json_encode([
                    'error' => 'Ya existe un ticket con ese titulo',
                ]);
                $conn->send($response);
                return;
            }
        }


        //guardar el ticket
        $ticket = new Tickets();
        $ticket->title = $data->data->title;
        $ticket->description = $data->data->description;
        $ticket->room_id = $room->id;
        //generar slug
        $ticket->slug = $room->slug . '-' .  $data->data->title;
        $ticket->save();

        //enviar al usuario que creo el ticket
        $response = [
            'event' => 'ticket-created',
            'data' => [
                'room_slug' => $room_slug,
                'ticket' => [
                    'title' => $ticket->title,
                    'description' => $ticket->description,
                    'slug' => $ticket->slug,
                ],
            ],
        ];

        $conn->send(json_encode($response));

        //enviar actualizacion del listado de tickets a todos los usuarios de la sala
    }

    public function sendUsersInRoom($slug)
    {
        $users = [];
        foreach ($this->rooms[$slug] as $user) {
            $users[] = [
                'username' => $user['username'],
                'image' => $user['image'],
            ];
        }

        $response = [
            'event' => 'users-in-room',
            'data' => [
                'room_slug' => $slug,
                'users' => $users,
            ],
        ];

        foreach ($this->rooms[$slug] as $user) {
            $user['conn']->send(json_encode($response));
        }
    }

    public function onLeftRoom(ConnectionInterface $conn, $data)
    {
        $user = User::where('api_token', $data->jwt)->first();
        $room_slug = $data->room_slug;



        if (!array_key_exists($room_slug, $this->rooms)) {
            $this->rooms[$room_slug] = [];
        }

        $this->rooms[$room_slug][$conn->resourceId] = [
            'conn' => $conn,
            'username' => $user->username,
        ];

        $response = [
            'event' => 'user-left',
            'data' => [
                'room_slug' => $room_slug,
                'username' => $user->username,
                'users' => [
                    [
                        'username' => $user->username,
                        'image' => $user->image,
                    ],
                ],
                'image' => $user->image,
            ],
        ];

        foreach ($this->rooms[$room_slug] as $user) {
            $user['conn']->send(json_encode($response));
        }
    }

    public function onJoinRoom(ConnectionInterface $conn, $data)
    {
        //buscar usuario por el token jwt y obtener su nombre, despues obtener el slug de la sala
        $user = User::where('api_token', $data->jwt)->first();
        $room_slug = $data->room_slug;

        //si no existe la sala, crearla
        if (!array_key_exists($room_slug, $this->rooms)) {
            $this->rooms[$room_slug] = [];
        }

        //agregar usuario a la sala
        $this->rooms[$room_slug][$conn->resourceId] = [
            'conn' => $conn,
            'username' => $user->username,
            'image' => asset($user->image),
        ];


        //refrescar lista de usuarios en la sala
        $this->sendUsersList($conn, $data, $room_slug);
    }

    public function sendUsersList(ConnectionInterface $conn, $data, $slug)
    {
        //obtener usuarios de la sala a partir del slug
        $users = [];
        foreach ($this->rooms[$slug] as $user) {
            $users[] = [
                'username' => $user['username'],
                'image' => $user['image'],
            ];
        }

        //enviar lista de usuarios a TODOS los usuarios de la sala
        $response = [
            'event' => 'users-in-room',
            'data' => [
                'room_slug' => $slug,
                'users' => $users,
            ],
        ];

        foreach ($this->rooms[$slug] as $user) {
            $user['conn']->send(json_encode($response));
        }
    }
}
