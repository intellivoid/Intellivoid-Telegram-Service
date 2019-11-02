<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class UserAgentNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class UserAgentNotFoundException extends Exception
    {
        /**
         * UserAgentNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The user agent was not found in the Database', ExceptionCodes::UserAgentNotFoundException, null);
        }
    }