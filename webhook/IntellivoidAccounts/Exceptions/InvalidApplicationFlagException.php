<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidApplicationFlagException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidApplicationFlagException extends Exception
    {
        /**
         * InvalidApplicationFlagException constructor.
         */
        public function __construct()
        {
            parent::__construct('The given Application Flag is invalid', ExceptionCodes::InvalidApplicationFlagException, null);
        }
    }