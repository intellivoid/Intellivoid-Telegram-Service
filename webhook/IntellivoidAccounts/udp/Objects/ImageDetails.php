<?php


    namespace udp\Objects;


    use udp\Abstracts\ImageType;

    /**
     * Class ImageDetails
     * @package udp\Objects
     */
    class ImageDetails
    {
        /**
         * @var int
         */
        public $Width;

        /**
         * @var int
         */
        public $Height;

        /**
         * @var ImageType
         */
        public $ImageType;
    }