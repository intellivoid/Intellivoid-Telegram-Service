<?php


    namespace udp\Exceptions;


    use Exception;

    /**
     * Class AvatarNotFoundException
     * @package udp\Exceptions
     */
    class AvatarNotFoundException extends Exception
    {
        /**
         * AvatarNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The requested avatar was not found');
        }
    }