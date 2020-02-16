<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class SubscriptionPromotionNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class SubscriptionPromotionNotFoundException extends Exception
    {
        /**
         * SubscriptionPromotionNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subscription promotion was not found", ExceptionCodes::SubscriptionPromotionNotFoundException, null);
        }
    }