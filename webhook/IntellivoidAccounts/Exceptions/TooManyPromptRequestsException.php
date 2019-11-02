<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class TooManyPromptRequestsException
     * @package IntellivoidAccounts\Exceptions
     */
    class TooManyPromptRequestsException extends Exception
    {
        /**
         * TooManyPromptRequestsException constructor.
         */
        public function __construct()
        {
            parent::__construct("There are too many requests at the moment", ExceptionCodes::TooManyPromptRequestsException, null);
        }
    }