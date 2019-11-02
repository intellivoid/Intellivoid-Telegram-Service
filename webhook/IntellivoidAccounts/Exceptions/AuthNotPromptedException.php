<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthNotPromptedException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthNotPromptedException extends Exception
    {
        /**
         * AuthNotPromptedException constructor.
         */
        public function __construct()
        {
            parent::__construct('No auth prompt has been made', ExceptionCodes::AuthNotPromptedException);
        }
    }