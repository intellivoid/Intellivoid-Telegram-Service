<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthenticationRequestNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthenticationRequestNotFoundException extends Exception
    {
        /**
         * AuthenticationRequestNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The authentication request was not found', ExceptionCodes::AuthenticationRequestNotFoundException, null);
        }
    }