<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidUrlException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidUrlException extends Exception
    {
        /**
         * InvalidUrlException constructor.
         */
        public function __construct()
        {
            parent::__construct("The given URL is invalid", ExceptionCodes::InvalidUrlException, null);
        }
    }