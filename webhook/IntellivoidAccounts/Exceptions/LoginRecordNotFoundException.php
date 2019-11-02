<?php


    namespace IntellivoidAccounts\Exceptions;

    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class LoginRecordNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class LoginRecordNotFoundException extends Exception
    {
        /**
         * LoginRecordNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct("The login record was not found in the database", ExceptionCodes::LoginRecordNotFoundException, null);
        }
    }