<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidCyclePriceException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidCyclePriceException extends Exception
    {
        /**
         * InvalidCyclePriceException constructor.
         */
        public function __construct()
        {
            parent::__construct("The cycle price is invalid, it cannot be less than 0", ExceptionCodes::InvalidCyclePriceException, null);
        }
    }