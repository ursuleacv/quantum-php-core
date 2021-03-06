<?php

/**
 * Quantum PHP Framework
 * 
 * An open source software development framework for PHP
 * 
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 1.0.0
 */

namespace Quantum\Http;

use Quantum\Http\HttpRequest;

/**
 * Request Class
 * 
 * Receive requests 
 * 
 * @package Quantum
 * @subpackage Http
 * @category Http
 */
class Request {

    /**
     * __call magic
     * 
     * @param string $function The function name
     * @param array $arguments 
     * @return mixed
     */
    public function __call($function, $arguments) {
        return HttpRequest::$function(...$arguments);
    }

    /**
     * __callStatic magic
     * 
     * @param string $function The function name
     * @param array $arguments 
     * @return mixed
     */
    public static function __callStatic($function, $arguments) {
        return HttpRequest::$function(...$arguments);
    }

}
