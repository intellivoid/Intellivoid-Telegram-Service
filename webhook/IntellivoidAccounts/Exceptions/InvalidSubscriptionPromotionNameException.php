<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidSubscriptionPromotionNameException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidSubscriptionPromotionNameException extends Exception
    {
        /**
         * InvalidSubscriptionPromotionNameException constructor.
         */
        public function __construct()
        {
            parent::__construct("The subscription's promotion code is invalid", ExceptionCodes::InvalidSubscriptionPromotionNameException, null);
        }
    }