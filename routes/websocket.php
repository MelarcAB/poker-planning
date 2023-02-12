<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\WebSocketController;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketController()
        )
    ),
    8080
);

$server->run();
