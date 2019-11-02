<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidRequestPermissionException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidRequestPermissionException extends Exception
    {
        /**
         * InvalidRequestPermissionException constructor.
         */
        public function __construct()
        {
            parent::__construct("The given permission is not valid", ExceptionCodes::InvalidRequestPermissionException, null);
        }
    }