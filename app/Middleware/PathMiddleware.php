<?php

namespace App\Middleware;

/**
 * Description of ValidationErrorsMiddleware
 *
 * @author Comtech
 */
class PathMiddleware extends Middleware {

    public function __invoke($request, $response, $next) {

        $virtualPath = $request->getUri()->getPath();
        
        $response = $next($request, $response);
        
        return $response;
    }
}
