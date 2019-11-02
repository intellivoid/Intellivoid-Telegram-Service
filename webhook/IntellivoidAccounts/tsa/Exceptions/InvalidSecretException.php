<?php


    namespace tsa\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class InvalidSecretException
     * @package tsa\Exceptions
     */
    class InvalidSecretException extends Exception
    {
        /**
         * InvalidSecretException constructor.
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($code = 0, Throwable $previous = null)
        {
            parent::__construct("The secret signature is invalid or empty", $code, $previous);
        }
    }