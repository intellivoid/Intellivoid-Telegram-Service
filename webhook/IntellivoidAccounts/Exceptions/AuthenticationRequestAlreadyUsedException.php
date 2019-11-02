<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthenticationRequestAlreadyUsedException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthenticationRequestAlreadyUsedException extends Exception
    {
        /**
         * AuthenticationRequestAlreadyUsedException constructor.
         */
        public function __construct()
        {
            parent::__construct('The authentication request ID was already used', ExceptionCodes::AuthenticationRequestAlreadyUsedException, null);
        }
    }