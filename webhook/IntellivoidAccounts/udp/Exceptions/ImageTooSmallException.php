<?php


    namespace udp\Exceptions;


    use Exception;

    /**
     * Class ImageTooSmallException
     * @package udp\Exceptions
     */
    class ImageTooSmallException extends Exception
    {
        /**
         * ImageTooSmallException constructor.
         */
        public function __construct()
        {
            parent::__construct('The image dimensions must be 128x128 pixels or greater');
        }
    }