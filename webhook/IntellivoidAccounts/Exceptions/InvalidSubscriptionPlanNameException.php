<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidSubscriptionPlanNameException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidSubscriptionPlanNameException extends Exception
    {
        /**
         * InvalidSubscriptionPlanNameException constructor.
         */
        public function __construct()
        {
            parent::__construct("The name for the subscription's plan name is invalid", ExceptionCodes::InvalidSubscriptionPlanNameException, null);
        }
    }