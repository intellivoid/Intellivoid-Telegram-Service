<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class TelegramApiException
     * @package IntellivoidAccounts\Exceptions
     */
    class TelegramApiException extends Exception
    {
        /**
         * TelegramApiException constructor.
         */
        public function __construct()
        {
            parent::__construct("The request to Telegram's API Endpoint has failed", ExceptionCodes::TelegramApiException, null);
        }
    }