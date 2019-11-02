<?php


    namespace udp\Managers;

    use finfo;
    use udp\Classes\SecurityVerification;
    use udp\Exceptions\FileUploadException;
    use udp\Exceptions\SystemException;
    use udp\Exceptions\UnsupportedFileTypeException;

    /**
     * Class TemporaryFileManager
     * @package udp\Managers
     */
    class TemporaryFileManager
    {
        /**
         * @var string
         */
        private $directory_location;

        /**
         * TemporaryFileManager constructor.
         * @param string $directory_location
         */
        public function __construct(string $directory_location)
        {
            $this->directory_location = $directory_location;
        }

        /**
         * Accepts a file upload request and saves it into the temporary file directory, returns the full
         * location whe done.
         *
         * @return string
         * @throws SystemException
         * @throws UnsupportedFileTypeException
         * @throws FileUploadException
         */
        public function accept_upload(): string
        {
            // Verify the upload
            SecurityVerification::verify_upload();

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $allowed_types = array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            );
            $extension = array_search($finfo->file($_FILES['user_av_file']['tmp_name']), $allowed_types, true);
            $output_file = sprintf($this->directory_location . DIRECTORY_SEPARATOR . '%s.%s',
                hash('haval128,3', $_FILES['user_av_file']['tmp_name']), $extension
            );

            // Save file to server
            if (!move_uploaded_file($_FILES['user_av_file']['tmp_name'], $output_file))
            {
                throw new SystemException("Failed to move uploaded file");
            }

            return $output_file;
        }
    }