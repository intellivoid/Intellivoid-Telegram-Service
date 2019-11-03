<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthPromptDeniedException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthPromptDeniedException extends Exception
    {
        /**
         * AuthPromptDeniedException constructor.
         */
        public function __construct()
        {
            parent::__construct("The authentication prompt has been denied", ExceptionCodes::AuthPromptDeniedException, null);
        }
    }