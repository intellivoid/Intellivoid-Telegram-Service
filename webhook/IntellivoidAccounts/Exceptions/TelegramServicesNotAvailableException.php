<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class TelegramServicesNotAvailableException
     * @package IntellivoidAccounts\Exceptions
     */
    class TelegramServicesNotAvailableException extends Exception
    {
        /**
         * TelegramServicesNotAvailableException constructor.
         */
        public function __construct()
        {
            parent::__construct("The Telegram Service is not available", ExceptionCodes::TelegramServicesNotAvailableException, null);
        }
    }