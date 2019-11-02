<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;

    /**
     * Class TelegramActionFailedException
     * @package IntellivoidAccounts\Exceptions
     */
    class TelegramActionFailedException extends Exception
    {
        /**
         * TelegramActionFailedException constructor.
         * @param string $message
         * @param int $code
         */
        public function __construct($message = "", $code = 0)
        {
            parent::__construct($message, $code);
        }
    }