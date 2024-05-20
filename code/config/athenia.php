<?php

return [

    'messaging_services' => [
        'slack_enabled' => env('ATHENIA_SLACK_ENABLED', false),
        'sms_enabled' => env('ATHENIA_SMS_ENABLED', false),
        'push_enabled' => env('ATHENIA_PUSH_ENABLED', false),
    ]
];