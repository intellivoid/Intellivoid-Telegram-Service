<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class SubscriptionNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class SubscriptionNotFoundException extends Exception
    {
        /**
         * SubscriptionNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subscription was not found in the database", ExceptionCodes::SubscriptionNotFoundException, null);
        }
    }