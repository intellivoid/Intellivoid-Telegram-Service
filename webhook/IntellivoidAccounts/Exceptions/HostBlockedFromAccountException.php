<?php


    namespace IntellivoidAccounts\Exceptions;

    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class HostBlockedFromAccountException
     * @package IntellivoidAccounts\Exceptions
     */
    class HostBlockedFromAccountException extends Exception
    {
        /**
         * HostBlockedFromAccountException constructor.
         */
        public function __construct()
        {
            parent::__construct('The host is blocked from accessing this account', ExceptionCodes::HostBlockedFromAccountException, null);
        }
    }