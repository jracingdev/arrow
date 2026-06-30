<?php

return [
    'api_key' => env('FIREBASE_APIKEY'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
    'database_url' => env('FIREBASE_DATABASE_URL'),
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    'messaging_sender_id' => env('FIREBASE_MESSAAGING_SENDER_ID'),
    'app_id' => env('FIREBASE_APP_ID'),
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),

    'client' => [
        'apiKey' => env('FIREBASE_APIKEY'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
        'databaseURL' => env('FIREBASE_DATABASE_URL'),
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAAGING_SENDER_ID'),
        'appId' => env('FIREBASE_APP_ID'),
        'measurementId' => env('FIREBASE_MEASUREMENT_ID'),
    ],
];
