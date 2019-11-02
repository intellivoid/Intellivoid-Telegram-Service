<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthPromptExpiredException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthPromptExpiredException extends Exception
    {
        /**
         * AuthPromptExpiredException constructor.
         */
        public function __construct()
        {
            parent::__construct("The auth prompt has expired and is no longer valid", ExceptionCodes::AuthPromptExpiredException,  null);
        }
    }