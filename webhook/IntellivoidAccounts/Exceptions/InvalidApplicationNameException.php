<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;
    use Throwable;

    /**
     * Class InvalidApplicationNameException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidApplicationNameException extends Exception
    {
        /**
         * InvalidApplicationNameException constructor.
         */
        public function __construct()
        {
            parent::__construct('The given Application name is invalid', ExceptionCodes::InvalidApplicationNameException, null);
        }
    }