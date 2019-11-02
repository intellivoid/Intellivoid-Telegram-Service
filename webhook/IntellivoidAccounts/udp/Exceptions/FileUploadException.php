<?php


    namespace udp\Exceptions;

    use Exception;

    /**
     * Class FileUploadException
     * @package udp\Exceptions
     */
    class FileUploadException extends Exception
    {
        /**
         * FileUploadException constructor.
         * @param string $message
         */
        public function __construct(string $message)
        {
            parent::__construct($message);
        }
    }