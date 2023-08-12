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
                $port = 8090;

                if (app()->environment('production')) {
                    // Rutas para el certificado y la clave privada.
                    // Asegúrate de que estén configuradas correctamente y sean accesibles.
                    //WEBSOCKETS_SSL_LOCAL_CERT y WEBSOCKETS_SSL_LOCAL_PK
                    $sslOptions = [
                        'local_cert' => '/path_to_your/fullchain.pem',    // tu certificado
                        'local_pk' => '/path_to_your/privkey.pem',        // tu clave privada
                        'verify_peer' => false
                    ];


                    $loop = \React\EventLoop\Loop::get();
                    $socket = new \React\Socket\Server('0.0.0.0:' . $port, $loop);
                    $socket = new \React\Socket\SecureServer($socket, $loop, $sslOptions);
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
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
