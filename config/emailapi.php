<?php
/*
|--------------------------------------------------------------------------
| Email Api Config
|--------------------------------------------------------------------------
|
|
*/

return [
    'platforms' => [
        'postmark' => [
            'api_token' => env('POSTMARK_API_TOKEN', '')
        ],
        'mailgun' => [],
        'mandrill' => [],
        'ses' => [],
    ],

    'queue_connections' => [
        'database' => [
            'driver' => 'database',
            'table' => 'email_api_jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],
    ]
];
