<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class ApplicationAlreadyExistsException
     * @package IntellivoidAccounts\Exceptions
     */
    class ApplicationAlreadyExistsException extends Exception
    {
        /**
         * ApplicationAlreadyExistsException constructor.
         */
        public function __construct()
        {
            parent::__construct("The Application already exists", ExceptionCodes::ApplicationAlreadyExistsException, null);
        }
    }