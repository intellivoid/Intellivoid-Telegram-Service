<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class ApplicationAccessNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class ApplicationAccessNotFoundException extends Exception
    {
        /**
         * ApplicationAccessNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The requested Application Access was not found', ExceptionCodes::ApplicationAccessNotFoundException, null);
        }
    }