<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;


    /**
     * Class ApplicationNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class ApplicationNotFoundException extends Exception
    {
        /**
         * ApplicationNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The application was not found in the database', ExceptionCodes::ApplicationNotFoundException, null);
        }
    }