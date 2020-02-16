<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidBillingCycleException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidBillingCycleException extends Exception
    {
        /**
         * InvalidBillingCycleException constructor.
         */
        public function __construct()
        {
            parent::__construct("The given billing cycle is invalid, it cannot be less than 0", ExceptionCodes::InvalidBillingCycleException, null);
        }
    }