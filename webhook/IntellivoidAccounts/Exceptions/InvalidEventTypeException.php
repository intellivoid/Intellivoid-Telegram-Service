<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidEventTypeException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidEventTypeException extends Exception
    {
        /**
         * InvalidEventTypeException constructor.
         */
        public function __construct()
        {
            parent::__construct('The given event type is invalid', ExceptionCodes::InvalidEventTypeException);
        }
    }