<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WebSocket Server Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the Node.js WebSocket server
    |
    */

    'node_server_url' => env('WEBSOCKET_NODE_SERVER_URL', 'http://localhost:3000'),

    'api_key' => env('WEBSOCKET_API_KEY', 'laravel-server-1-key'),

    'timeout' => env('WEBSOCKET_TIMEOUT', 10), // seconds

    'enabled' => env('WEBSOCKET_ENABLED', true),
];
