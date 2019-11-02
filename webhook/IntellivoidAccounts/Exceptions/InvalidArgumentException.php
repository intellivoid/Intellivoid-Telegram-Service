<?php


    namespace IntellivoidAccounts\Exceptions;

    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidArgumentException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidArgumentException extends Exception
    {
        /**
         * InvalidArgumentException constructor.
         */
        public function __construct()
        {
            parent::__construct("The given argument is invalid", ExceptionCodes::InvalidArgumentException, null);
        }
    }