<?php

namespace App\Middleware;

/**
 * Description of CsrfViewMiddleware
 *
 * @author Comtech
 */
class AuthMiddleware extends Middleware {
    
    public function __invoke($request, $response, $next) {
       
        if(!$this->container->auth->check()) {
            return $response->withRedirect($this->container->router->pathFor('admin_signin'));
        }
       
       
       $response = $next($request, $response);
       
       return $response;
    }
}