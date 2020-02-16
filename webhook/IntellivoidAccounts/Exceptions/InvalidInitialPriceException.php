<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidInitialPriceException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidInitialPriceException extends Exception
    {
        /**
         * InvalidInitialPriceException constructor.
         */
        public function __construct()
        {
            parent::__construct("The initial price cannot be lower than 0", ExceptionCodes::InvalidInitialPriceException, null);
        }
    }