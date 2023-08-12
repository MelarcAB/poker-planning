<?php

use BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize;

return [

    'dashboard' => [
        'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),
    ],


    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'path' => env('PUSHER_APP_PATH'),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
            'encrypted' => env('WEBSOCKETS_ENCRYPTED', false),

        ],
    ],

    'app_provider' => BeyondCode\LaravelWebSockets\Apps\ConfigAppProvider::class,

    'allowed_origins' => [
        //
    ],
    'max_request_size_in_kb' => 250,
    'path' => 'laravel-websockets',
    'middleware' => [
        'web',
        Authorize::class,
    ],
    'statistics' => [
        'model' => \BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry::class,

        'logger' => BeyondCode\LaravelWebSockets\Statistics\Logger\HttpStatisticsLogger::class,

        'interval_in_seconds' => 60,
        'delete_statistics_older_than_days' => 60,
        'perform_dns_lookup' => false,
    ],

    /*
     * Define the optional SSL context for your WebSocket connections.
     * You can see all available options at: http://php.net/manual/en/context.ssl.php
     */
    'ssl' => [
        /*
         * Path to local certificate file on filesystem. It must be a PEM encoded file which
         * contains your certificate and private key. It can optionally contain the
         * certificate chain of issuers. The private key also may be contained
         * in a separate file specified by local_pk.
         */
        'local_cert' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT', null),


        'local_pk' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_PK', null),


        'passphrase' => env('LARAVEL_WEBSOCKETS_SSL_PASSPHRASE', null),
    ],


    'channel_manager' => \BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManagers\ArrayChannelManager::class,
];
