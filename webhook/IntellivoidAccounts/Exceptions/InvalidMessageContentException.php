<?php


    namespace IntellivoidAccounts\Exceptions;

    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class InvalidMessageContentException
     * @package IntellivoidAccounts\Exceptions
     */
    class InvalidMessageContentException extends Exception
    {
        /**
         * InvalidMessageContentException constructor.
         */
        public function __construct()
        {
            parent::__construct("The message content either invalid or too big to process", ExceptionCodes::InvalidMessageContentException, null);
        }
    }