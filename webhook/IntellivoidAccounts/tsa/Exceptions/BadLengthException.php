<?php


    namespace tsa\Exceptions;

    use Exception;
    use Throwable;

    /**
     * Class BadLengthException
     * @package tsa\Exceptions
     */
    class BadLengthException extends Exception
    {
        /**
         * BadLengthException constructor.
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($code = 0, Throwable $previous = null)
        {
            parent::__construct("Valid secret lengths are 80 to 640 bits", $code, $previous);
        }
    }