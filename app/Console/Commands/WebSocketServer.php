<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use App\Http\Controllers\SocketController;

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $port = 8090;
            if (app()->environment('production')) {
                // Asegúrate de que este código esté actualizado para usar SSL en producción
            } else {
                $server = IoServer::factory(
                    new HttpServer(
                        new WsServer(
                            new SocketController()
                        )
                    ),
                    $port
                );
                $server->run();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
