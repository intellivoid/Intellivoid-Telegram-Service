<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidMessageSubjectException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidMessageSubjectException extends Exception
    {
        /**
         * InvalidMessageSubjectException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subject is invalid or too long", ExceptionCodes::InvalidMessageSubjectException, null);
        }
    }