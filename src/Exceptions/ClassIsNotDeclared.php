<?php

namespace JosbertM\DocblocksWriter\Exceptions;

use Exception;

/**
 * This exception is thrown when a class name
 * passed to Writer is not declared.
 */
class ClassIsNotDeclared extends Exception
{
    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $message = "The class [{$class}] is not declared.";

        parent::__construct($message);
    }
}
