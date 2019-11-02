<?php


    namespace udp;

    use Exception;
    use udp\Classes\DisplayPictureGenerator;
    use udp\Managers\ProfilePictureManager;
    use udp\Managers\TemporaryFileManager;

    include_once(__DIR__ . DIRECTORY_SEPARATOR  . 'Interfaces' . DIRECTORY_SEPARATOR . 'GeneratorInterface.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'ImageType.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'BaseGenerator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'DisplayPictureGenerator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'GdGenerator.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'ImageProcessor.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'SecurityVerification.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'SvgGenerator.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'AvatarNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'FileUploadException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'ImageTooSmallException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidImageException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'SystemException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'UnsupportedFileTypeException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'ProfilePictureManager.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'TemporaryFileManager.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'ImageDetails.php');

    /**
     * Class udp
     * @package udp
     */
    class udp
    {
        /**
         * @var DisplayPictureGenerator
         */
        private $displayPictureGenerator;

        /**
         * @var string
         */
        private $tmp_data_location;

        /**
         * @var string
         */
        private $user_pics_location;

        /**
         * @var string
         */
        private $storage_location;

        /**
         * @var TemporaryFileManager
         */
        private $temporary_file_manager;

        /**
         * @var ProfilePictureManager
         */
        private $profile_picture_manager;

        /**
         * udp constructor.
         * @param string $storage_location
         * @throws Exception
         */
        public function __construct(string $storage_location)
        {
            $this->displayPictureGenerator = new DisplayPictureGenerator();
            $this->storage_location = $storage_location;
            $this->user_pics_location = $storage_location . DIRECTORY_SEPARATOR . 'user_pics';
            $this->tmp_data_location = $storage_location . DIRECTORY_SEPARATOR . 'tmp';

            if(file_exists($this->user_pics_location) == false)
            {
                mkdir($this->user_pics_location);
            }

            if(file_exists($this->tmp_data_location) == false)
            {
                mkdir($this->tmp_data_location);
            }

            $this->temporary_file_manager = new TemporaryFileManager($this->tmp_data_location);
            $this->profile_picture_manager = new ProfilePictureManager($this->user_pics_location, $this->tmp_data_location, $this);
        }

        /**
         * @return DisplayPictureGenerator
         */
        public function getDisplayPictureGenerator()
        {
            return $this->displayPictureGenerator;
        }

        /**
         * @return TemporaryFileManager
         */
        public function getTemporaryFileManager(): TemporaryFileManager
        {
            return $this->temporary_file_manager;
        }

        /**
         * @return ProfilePictureManager
         */
        public function getProfilePictureManager(): ProfilePictureManager
        {
            return $this->profile_picture_manager;
        }
    }