<?php

use Illuminate\Database\Capsule\Manager as Capsule;

use App\Middleware\LanguageMiddleware;

use App\Middleware\Language;

use Monolog\Logger;

use Monolog\Handler\StreamHandler;

use Monolog\Handler\FingersCrossedHandler;

$container = $app->getContainer();

$container['auth'] = function($container) {
    return new App\Auth\Auth;
};

$container['logo_upload_directory'] = __DIR__ . '\\..\\..\\public\\img\\logo';
$container['category_upload_directory'] = __DIR__ . '\\..\\..\\public\\img\\category';
$container['subcategory_upload_directory'] = __DIR__ . '\\..\\..\\public\\img\\subcategory';
$container['product_upload_directory'] = __DIR__ . '\\..\\..\\public\\img\\product';

//$container['logo_upload_directory'] = '/var/www/html/restaurant-v1_1/public/img/logo';  // linux
//$container['category_upload_directory'] = '/var/www/html/restaurant-v1_1/public/img/category'; // linux
//$container['subcategory_upload_directory'] = '/var/www/html/restaurant-v1_1/public/img/subcategory'; // linux
//$container['product_upload_directory'] = '/var/www/html/restaurant-v1_1/public/img/product';

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../../resources/views', [
        //'cache' => '/../../cache',
        'cache' => false,
    ]);

    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    
    return $view;
};

$app->add(new LanguageMiddleware(
    $container->settings['languages'],
    $container->settings['defaultLanguage']
));

$container['locale'] = new Language($container->settings['defaultLanguage']);

$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        $container->errorLogger->addError('405 by ' . $_SERVER['REMOTE_ADDR'], array($_SERVER['REQUEST_URI']));
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->withRedirect('http://localhost/restaurant-slim-v1/public/index/gr', 301);    
            //->write('Back to <a href="http://localhost/restaurant-slim-v1/public/">Home Page</a>');
            //->write('Method must be one of: ' . implode(', ', $methods));
    };
};

$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $container->errorLogger->addError('404 by ' . $_SERVER['REMOTE_ADDR'], array($_SERVER['REQUEST_URI']));
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found!<br>Back to <a href="http://localhost/restaurant-slim-v1/public/index/gr">Home Page!</a>');
            //->write('Page not found');
    };
};

$container['errorLogger'] = function($container) {
    $logger = new Logger('FrontLogger');
    $filename = __DIR__ . '\..\..\log\error.log';
    //$filename = __DIR__ . '/../../log/error.log'; // Linux
    $stream = new StreamHandler($filename, Logger::DEBUG);
    $fingersCrossed = new FingersCrossedHandler($stream, Logger::ERROR);
    $logger->pushHandler($fingersCrossed);

    return $logger;
};

$capsule = new Capsule;

$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();

$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

$container['csrf'] = function($container) {
    return new Slim\Csrf\Guard;
};

$app->add(new App\Middleware\ValidationErrorsMiddleware($container));

$app->add(new App\Middleware\CsrfViewMiddleware($container));

//$app->add(new App\Middleware\PathMiddleware($container));

$app->add($container->csrf);

require 'register.php';