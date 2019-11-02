<?php


    namespace tsa\Exceptions;


    use Throwable;

    class Base32DecodingException extends \Exception
    {
        public function __construct($code = 0, Throwable $previous = null)
        {
            parent::__construct("The base32 data cannot be decoded", $code, $previous);
        }
    }