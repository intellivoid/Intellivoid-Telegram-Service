<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class SubscriptionNotActiveException
     * @package IntellivoidAccounts\Exceptions
     */
    class SubscriptionNotActiveException extends Exception
    {
        /**
         * SubscriptionNotActiveException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subscription is not currently active", ExceptionCodes::SubscriptionNotActiveException, null);
        }
    }