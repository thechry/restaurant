<?php

namespace App\Middleware;

/**
 * Description of Middleware
 *
 * @author Comtech
 */
class Middleware {
    protected $container;
    
    public function __construct($container) {
        $this->container = $container;
    }
}