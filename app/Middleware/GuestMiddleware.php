<?php

namespace App\Middleware;

/**
 * Description of CsrfViewMiddleware
 *
 * @author Comtech
 */
class GuestMiddleware extends Middleware {
    
    public function __invoke($request, $response, $next) {
       
        if($this->container->auth->check()) {
            return $response->withRedirect($this->container->router->pathFor('index'));
        }
       
       
       $response = $next($request, $response);
       
       return $response;
    }
}