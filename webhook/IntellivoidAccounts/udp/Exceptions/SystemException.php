<?php


    namespace udp\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class SystemException
     * @package udp\Exceptions
     */
    class SystemException extends Exception
    {
        /**
         * SystemException constructor.
         * @param $message
         */
        public function __construct($message)
        {
            parent::__construct($message);
        }
    }