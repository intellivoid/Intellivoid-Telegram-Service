<?php


    namespace udp\Exceptions;

    use Exception;

    /**
     * Class UnsupportedFileTypeException
     * @package udp\Exceptions
     */
    class UnsupportedFileTypeException extends Exception
    {
        /**
         * UnsupportedFileTypeException constructor.
         * @param $message
         */
        public function __construct($message)
        {
            parent::__construct($message);
        }
    }