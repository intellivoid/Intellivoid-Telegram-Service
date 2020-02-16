<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidInitialPriceShareException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidInitialPriceShareException extends Exception
    {
        /**
         * InvalidInitialPriceShareException constructor.
         */
        public function __construct()
        {
            parent::__construct("The initial price share is invalid, it cannot bless than 0 nor greater than the initial price", ExceptionCodes::InvalidInitialPriceShareException, null);
        }
    }