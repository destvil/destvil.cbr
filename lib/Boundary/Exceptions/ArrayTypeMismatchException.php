<?php

namespace destvil\cbr\Boundary\Exceptions;

use Throwable;

class ArrayTypeMismatchException extends CbrException
{
    public function __construct(string $type, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Array must be contains ' . $type . ' objects', $code, $previous);
    }
}