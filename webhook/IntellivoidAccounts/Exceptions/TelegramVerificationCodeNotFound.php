<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class TelegramVerificationCodeNotFound
     * @package IntellivoidAccounts\Exceptions
     */
    class TelegramVerificationCodeNotFound extends Exception
    {
        /**
         * TelegramVerificationCodeNotFound constructor
         */
        public function __construct()
        {
            parent::__construct('The Telegram verification code was not found in the Database', ExceptionCodes::TelegramVerificationCodeNotFound, null);
        }
    }