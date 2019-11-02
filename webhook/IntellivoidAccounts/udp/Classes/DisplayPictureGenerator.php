<?php /** @noinspection PhpUnused */


    namespace udp\Classes;


    /**
     * Class DisplayPictureGenerator
     * @package udp\Classes
     */
    class DisplayPictureGenerator
    {
        /**
         * @var GdGenerator
         */
        private $generator;

        /**
         * DisplayPictureGenerator constructor.
         * @throws \Exception
         */
        public function __construct()
        {
            $this->generator = new GdGenerator();
        }

        /**
         * @return GdGenerator
         */
        public function getGenerator()
        {
            return $this->generator;
        }

        /**
         * @param GdGenerator $generator
         */
        public function setGenerator($generator)
        {
            $this->generator = $generator;
        }

        /**
         * Display an Identicon image.
         *
         * @param string $string
         * @param int $size
         * @param string|array $color
         * @param string $backgroundColor
         * @throws \Exception
         */
        public function displayImage($string, $size = 64, $color = null, $backgroundColor = null)
        {
            header('Content-Type: '.$this->generator->getMimeType());
            echo $this->getImageData($string, $size, $color, $backgroundColor);
        }

        /**
         * Get an Identicon PNG image data.
         *
         * @param string $string
         * @param int $size
         * @param string|array $color
         * @param string $backgroundColor
         *
         * @return string
         * @throws \Exception
         */
        public function getImageData($string, $size = 64, $color = null, $backgroundColor = null)
        {
            return $this->generator->getImageBinaryData($string, $size, $color, $backgroundColor);
        }

        /**
         * Get an Identicon PNG image resource.
         *
         * @param string $string
         * @param int $size
         * @param string|array $color
         * @param string $backgroundColor
         *
         * @return string
         * @throws \Exception
         */
        public function getImageResource($string, $size = 64, $color = null, $backgroundColor = null)
        {
            return $this->generator->getImageResource($string, $size, $color, $backgroundColor);
        }

        /**
         * Get an Identicon PNG image data as base 64 encoded.
         *
         * @param string $string
         * @param int $size
         * @param string|array $color
         * @param string $backgroundColor
         *
         * @return string
         * @throws \Exception
         */
        public function getImageDataUri($string, $size = 64, $color = null, $backgroundColor = null)
        {
            return sprintf('data:%s;base64,%s', $this->generator->getMimeType(), base64_encode($this->getImageData($string, $size, $color, $backgroundColor)));
        }

        /**
         * Get the color of the Identicon
         *
         * Returns an array with RGB values of the Identicon's color. Colors may be NULL if no image has been generated
         * so far (e.g., when calling the method on a new Identicon()).
         *
         * @return array
         */
        public function getColor()
        {
            $colors = $this->generator->getColor();

            return [
                "r" => $colors[0],
                "g" => $colors[1],
                "b" => $colors[2]
            ];
        }
    }