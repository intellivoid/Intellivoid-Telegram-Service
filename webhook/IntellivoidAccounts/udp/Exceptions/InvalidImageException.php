<?php


    namespace udp\Exceptions;

    /**
     * Class InvalidImageException
     * @package udp\Exceptions
     */
    class InvalidImageException extends \Exception
    {
        /**
         * InvalidImageException constructor.
         * @param $message
         */
        public function __construct($message)
        {
            parent::__construct($message);
        }
    }