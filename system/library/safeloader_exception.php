<?php

/**
 * SafeLoaderException - custom exception for SafeLoader
 * 
 * @author  sv2109
 * @link    sv2109@gmail.com
*/

class SafeLoaderException extends Exception {
    protected $module;

    public function __construct($module, $message = "", $code = 0, ?Throwable $previous = null) {
        $this->module = $module;
        parent::__construct($message, $code, $previous);
    }

    public function getModule() {
        return $this->module;
    }
}