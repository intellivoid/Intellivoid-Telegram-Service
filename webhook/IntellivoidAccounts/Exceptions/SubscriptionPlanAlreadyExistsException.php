<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class SubscriptionPlanAlreadyExistsException
     * @package IntellivoidAccounts\Exceptions
     */
    class SubscriptionPlanAlreadyExistsException extends Exception
    {
        /**
         * SubscriptionPlanAlreadyExistsException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subscription plan already exists for this Application", ExceptionCodes::SubscriptionPlanAlreadyExistsException, null);
        }
    }