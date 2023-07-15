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
use App\Models\TicketsVotation;

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

                $this->onNewTicket($conn, $data);
                break;

            case 'vote':
                $this->onVote($conn, $data);
                break;

            case 'get-votes-user':
                $this->onGetVotes($conn, $data, $user = true);
                break;
            case 'get-votes':
                $this->onGetVotes($conn, $data);
                break;

            case 'remove-vote':
                $this->onRemoveVote($conn, $data);
                break;

            case 'submit-votes':
                $this->onSubmitVotes($conn, $data);
                break;
        }
    }

    public function onSubmitVotes(ConnectionInterface $conn, $data)
    {
        try {
            $room_slug = $data->room_slug;
            $jwt = $data->jwt;
            //ticket slug on data
            $ticket_slug = $data->data->ticket_slug;

            $user = User::where('api_token', $jwt)->first();
            $ticket = Tickets::where('slug', $ticket_slug)->first();
            //verificar que el usuario es el creador del grupo

            $room = Room::where('slug', $room_slug)->first();
            $group = Groups::where('id', $room->group_id)->first();

            //verificar que el usuario es el creador del grupo
            if ($group->user_id != $user->id) {
                $conn->send(json_encode([
                    'event' => 'error',
                    'data' => 'No eres el creador del grupo',
                ]));
                return;
            }

            //si el ticket visible = false se cambia a true, si es true se cambia a false
            if ($ticket->visible == "false") {
                $ticket->visible = "true";
            } else {
                $ticket->visible = "false";
            }
            $ticket->save();

            $this->sendVotesInRoomToPublicRoom($room_slug, $jwt, $conn, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function onRemoveVote(ConnectionInterface $conn, $data)
    {
        try {
            $room_slug = $data->room_slug;
            $ticket_slug = $data->ticket_slug;
            $jwt = $data->jwt;

            $user = User::where('api_token', $jwt)->first();
            $ticket = Tickets::where('slug', $ticket_slug)->first();
            $votation = TicketsVotation::where('user_id', $user->id)->where('ticket_id', $ticket->id)->first();
            if ($votation) {

                $votation->delete();
            }

            $this->sendVotesInRoomToPublicRoom($room_slug, $jwt, $conn);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    public function onGetVotes(ConnectionInterface $conn, $data, $user = false)
    {
        try {
            //obtener todos los votos de la sala y devolverlos solo al usuario que lo solicita
            $room_slug = $data->room_slug;
            $jwt = $data->jwt;

            if ($user) {
                print "User {$jwt} get votes in room {$room_slug}\n";
                $this->sendVotesInRoomToUser($room_slug, $jwt, $conn);
            } else {
                print "User {$jwt} get votes in room {$room_slug}\n";
                $this->sendVotesInRoomToPublicRoom($room_slug, $jwt, $conn);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function sendVotesInRoomToUser($room_slug, $jwt, $conn)
    {
        //obtener el room y todas las votaciones para todos los tickets
        $room = Room::where('slug', $room_slug)->first();
        $tickets = Tickets::where('room_id', $room->id)->get();
        $votes = [];
        foreach ($tickets as $ticket) {
            $votations =  $ticket->votations;
            // var_dump($votations);
            print $ticket->slug . " count " . ($votations)->count() . "\n";
            if ($votations->count() > 0) {
                // $votes[$ticket->slug] = $votations->toArray();
                foreach ($votations as $votation) {
                    $votes[] = [
                        'ticket_slug' => $ticket->slug,
                        'user_id' => $votation->user_id,
                        'user_name' => $votation->user->username,
                        'vote' => $votation->vote,
                        'visible' => $ticket->visible,

                    ];
                }
            }
        }
        //devolver los votos de la sala al usuario que lo solicito
        $response =  json_encode([
            'event' => 'votes',
            'data' => $votes,
        ]);
        $user = User::where('api_token', $jwt)->first();

        $conn->send($response);
    }


    public function sendVotesInRoomToPublicRoom($room_slug, $jwt, $conn, $revelation = false)
    {
        //obtener el room y todas las votaciones para todos los tickets
        $room = Room::where('slug', $room_slug)->first();
        $tickets = Tickets::where('room_id', $room->id)->get();
        $votes = [];
        foreach ($tickets as $ticket) {
            $votations =  $ticket->votations;
            // var_dump($votations);
            print $ticket->slug . " count " . ($votations)->count() . "\n";
            if ($votations->count() > 0) {
                // $votes[$ticket->slug] = $votations->toArray();
                foreach ($votations as $votation) {
                    $votes[] = [
                        'ticket_slug' => $ticket->slug,
                        'user_id' => $votation->user_id,
                        'user_name' => $votation->user->username,
                        'vote' => $votation->vote,
                        'visible' => $ticket->visible,
                    ];
                }
            }
        }
        //devolver los votos de la sala al usuario que lo solicito
        $response =  json_encode([
            'event' => 'votes',
            'data' => $votes,
        ]);
        //revelation
        if ($revelation) {
            $response =  json_encode([
                'event' => 'votes',
                'data' => $votes,
                'revelation' => true,
            ]);
        }

        foreach ($this->rooms[$room_slug] as $connection) {
            $connection['conn']->send($response);
        }
    }


    public function onVote(ConnectionInterface $conn, $data)
    {
        try {
            $room_slug = $data->room_slug;
            $ticket_slug = $data->data->ticket_slug;
            $user = User::where('api_token', $data->jwt)->first();
            //obtener el ticket
            $ticket = Tickets::where('slug', $ticket_slug)->first();
            //obtener el room
            $room = Room::where('slug', $room_slug)->first();
            //check si existe el voto para este ticket y user
            $votation = TicketsVotation::where('user_id', $user->id)->where('ticket_id', $ticket->id)->delete();
            //añadir el voto
            $votation = new TicketsVotation();
            $votation->user_id = $user->id;
            $votation->ticket_id = $ticket->id;

            //validar si el voto es visible o no (ticket visible)
            if ($ticket->visible == "true") {
                $response =  json_encode([
                    'error' => 'No puedes votar un ticket revelado',
                ]);
                $conn->send($response);
                return;
            }

            //value
            $votation->vote = $data->data->value;
            $votation->save();

            //devolver los votos de la sala
            // $this->sendVotesInRoom($room_slug);
            //sendVotesInRoomToPublicRoom
            $this->sendVotesInRoomToPublicRoom($room_slug, $data->jwt, $conn);
        } catch (\Exception $e) {
            $response =  json_encode([
                'error' => 'Error al votar',
            ]);
            print $e->getMessage();
            $conn->send($response);
            return;
        }
    }
    /*
    public function sendVotesInRoom($room_slug)
    {
        //obtener el room y todas las votaciones para todos los tickets
        $room = Room::where('slug', $room_slug)->first();
        $tickets = $room->tickets;
        $tickets_arr = [];
        foreach ($tickets as $ticket) {
            $aux = [];
            //de cada ticket obtener los votos y los usuarios
            $votations = $ticket->votations;
            foreach ($votations as $votation) {
                $tickets_arr[] = [
                    'user' => $votation->user->username,
                    'vote' => $votation->vote,
                    'ticket_slug' => $ticket->slug,
                ];
            }
        }
        $data = [
            'event' => 'votes-list',
            'data' => $tickets_arr,
        ];
        $response = json_encode($data);
        foreach ($this->rooms[$room_slug] as $connection) {
            $connection['conn']->send($response);
        }
    }
*/
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
        $this->sendTicketsInRoom($room_slug);
    }


    public function sendTicketsInRoom($slug)
    {
        //obtener la sala a partir del slug
        $room = Room::where('slug', $slug)->first();
        $tickets = $room->tickets;
        if (count($tickets) == 0 || $tickets == null) {
            $tickets = [];
        }

        $response = [
            'event' => 'update-tickets-list',
            'data' => [
                'room_slug' => $slug,
                'tickets' => $tickets,
            ],
        ];

        foreach ($this->rooms[$slug] as $user) {
            $user['conn']->send(json_encode($response));
        }
    }

    public function sendTicketsInRoomToUser($slug, $conn)
    {
        //obtener la sala a partir del slug
        $room = Room::where('slug', $slug)->first();
        $tickets = $room->tickets;
        if (count($tickets) == 0 || $tickets == null) {
            $tickets = [];
        }

        $response = [
            'event' => 'update-tickets-list',
            'data' => [
                'room_slug' => $slug,
                'tickets' => $tickets,
            ],
        ];

        $conn->send(json_encode($response));
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


        //verificar que el usuario no este en la sala
        $in_room = false;
        foreach ($this->rooms[$room_slug] as $users) {
            if ($users['username'] == $user->username) {
                $in_room = true;
            }
        }


        //agregar usuario a la sala
        if (!$in_room) {
            $this->rooms[$room_slug][$conn->resourceId] = [
                'conn' => $conn,
                'username' => $user->username,
                'image' => asset($user->image),
            ];
        }

        //refrescar lista de tickets en la sala
        $this->sendTicketsInRoomToUser($room_slug, $conn);
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
