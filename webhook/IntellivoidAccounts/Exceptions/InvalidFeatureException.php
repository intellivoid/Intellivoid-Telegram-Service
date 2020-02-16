<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidFeatureException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidFeatureException extends Exception
    {
        /**
         * InvalidFeatureException constructor.
         */
        public function __construct()
        {
            parent::__construct("The given value must be a feature object, it presents missing values", ExceptionCodes::InvalidFeatureException, null);
        }
    }