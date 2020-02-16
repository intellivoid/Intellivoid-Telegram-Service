<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidFundsValueException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidFundsValueException extends Exception
    {
        /**
         * InvalidFundsValueException constructor.
         */
        public function __construct()
        {
            parent::__construct("The value for the funds is invalid", ExceptionCodes::InvalidFundsValueException, null);
        }
    }