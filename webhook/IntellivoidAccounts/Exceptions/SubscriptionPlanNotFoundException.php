<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class SubscriptionPlanNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class SubscriptionPlanNotFoundException extends Exception
    {
        /**
         * SubscriptionPlanNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct("The Subscription Plan was not found in the database", ExceptionCodes::SubscriptionPlanNotFoundException, null);
        }
    }