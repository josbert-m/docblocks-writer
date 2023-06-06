<?php

namespace JosbertM\DocblocksWriter\Exceptions;

use Exception;

/**
 * This exception is thrown when
 * is impossible to map some Class.
 */
class ClassNotMappeable extends Exception
{
    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $message = "Class [{$class}] impossible to map.";

        parent::__construct($message);
    }
}
