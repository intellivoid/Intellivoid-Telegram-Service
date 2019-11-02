<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class OtlNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class OtlNotFoundException extends Exception
    {
        /**
         * OtlNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct("The one time login code record was not found in the Database", ExceptionCodes::OtlNotFoundException, null);
        }
    }