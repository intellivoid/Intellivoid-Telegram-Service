<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class SubscriptionPromotionAlreadyExistsException
     * @package IntellivoidAccounts\Exceptions
     */
    class SubscriptionPromotionAlreadyExistsException extends Exception
    {
        /**
         * SubscriptionPromotionAlreadyExistsException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subscription promotion already exists", ExceptionCodes::SubscriptionPromotionAlreadyExistsException, null);
        }
    }