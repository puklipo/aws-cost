<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'aws' => [
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID', ''),
            'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
        ],
        'region' => env('AWS_REGION', 'us-east-1'),
        'version' => 'latest',
    ],
];
