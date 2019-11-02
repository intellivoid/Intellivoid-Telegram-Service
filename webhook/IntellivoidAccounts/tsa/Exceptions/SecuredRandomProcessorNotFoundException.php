<?php


    namespace tsa\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class SecuredRandomProcessorNotFoundException
     * @package tsa\Exceptions
     */
    class SecuredRandomProcessorNotFoundException extends Exception
    {
        /**
         * SecuredRandomProcessorNotFoundException constructor.
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($code = 0, Throwable $previous = null)
        {
            parent::__construct("The system does not contain any form of secured random processors", $code, $previous);
        }
    }