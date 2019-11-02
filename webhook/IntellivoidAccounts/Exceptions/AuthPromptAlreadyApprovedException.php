<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class AuthPromptAlreadyApprovedException
     * @package IntellivoidAccounts\Exceptions
     */
    class AuthPromptAlreadyApprovedException extends Exception
    {
        /**
         * AuthPromptAlreadyApprovedException constructor.
         */
        public function __construct()
        {
            parent::__construct("The authentication prompt has already been approved", ExceptionCodes::AuthPromptAlreadyApprovedException, null);
        }
    }