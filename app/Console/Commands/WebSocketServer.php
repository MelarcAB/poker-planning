<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use App\Http\Controllers\SocketController;
use Exception;

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
                // Rutas para el certificado y la clave privada.
                $sslOptions = [
                    'local_cert' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT', null), // tu certificado
                    'local_pk' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_PK', null), // tu clave privada
                    'verify_peer' => false
                ];

                $loop = \React\EventLoop\Loop::get();
                $socket = new \React\Socket\Server('0.0.0.0:' . $port, $loop);
                $socket = new \React\Socket\SecureServer($socket, $loop, $sslOptions);
            } else {
                $socket = new \React\Socket\Server('0.0.0.0:' . $port, $loop);
            }

            $server = new IoServer(
                new HttpServer(
                    new WsServer(
                        new SocketController()
                    )
                ),
                $socket,
                $loop
            );
            $server->run();
        } catch (Exception $e) {
            // Consider logging more information.
            echo $e->getMessage();
        }
    }
}
