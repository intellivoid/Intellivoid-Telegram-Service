<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class HostNotKnownException
     * @package IntellivoidAccounts\Exceptions
     */
    class HostNotKnownException extends Exception
    {
        /**
         * HostNotKnownException constructor.
         */
        public function __construct()
        {
            parent::__construct("The host was not known or is associated with the given account", ExceptionCodes::HostNotKnownException, null);
        }
    }