<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => true,
        'db' => [
            'host' => 'localhost',
            'driver' => 'mysql',
            'database' => 'restaurant-v1',
            'username' => 'comtech',
            'password' => '1234!@#$',            
            //'driver' => 'pgsql',
            //'database' => 'restaurant-slim-v1',
            //'username' => 'comtech',
            //'password' => '123!@#',                        
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
        ],
        'defaultLanguage' => 'gr',
        'languages' => [
            'gr',
            'en',
        ],
    ]
];