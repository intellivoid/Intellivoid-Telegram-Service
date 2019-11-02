<?php


    namespace IPStack\Exceptions;

    use Exception;

    /**
     * Class LookupException
     * @package IPStack\Exceptions
     */
    class LookupException extends Exception
    {
        /**
         * LookupException constructor.
         * @param $message
         * @param $code
         */
        public function __construct($message, $code)
        {
            parent::__construct($message, $code, null);
        }
    }