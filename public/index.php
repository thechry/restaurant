<?php

use \Psr\Http\Message\ServerRequestInterface as Request;

use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config = require __DIR__ . '/../resources/settings.php';

$ct = new \Slim\Container($config);

$app = new \Slim\App($ct);

session_start();

require __DIR__ . '/../app/Bootstrap/Routes.php';

$app->run();