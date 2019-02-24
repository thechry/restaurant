<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => true,
        'db' => [
            'host' => 'host',
            'driver' => 'drv',
            'database' => 'db',
            'username' => 'name',
            'password' => 'name',            
            //'driver' => 'drv',
            //'database' => 'db',
            //'username' => 'name',
            //'password' => 'name',                        
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
