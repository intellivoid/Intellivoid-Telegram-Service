<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthenticationAccessNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthenticationAccessNotFoundException extends Exception
    {
        /**
         * AuthenticationAccessNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The Authentication Access record was not found', ExceptionCodes::AuthenticationAccessNotFoundException, null);
        }
    }