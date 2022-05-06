<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default RocketChat Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which RocketChat connections below you wish
    | to use as your default connection for all RocketChat notifications. Of course
    | you may use many connections at once using the RocketChat static methods or
    | RocketChatMessage connection method.
    |
    */

    'default' => env('ROCKETCHAT_CONNECTION', 'rocket-chat'),

    /*
    |--------------------------------------------------------------------------
    | RocketChat Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the RocketChat connections setup for your application.
    |
    */

    'connections' => [

        'rocket-chat' => [
            'url' => env('ROCKETCHAT_URL'),
            'token' => env('ROCKETCHAT_TOKEN'),
            'user_id' => env('ROCKETCHAT_USER_ID')
        ]

    ]
];
