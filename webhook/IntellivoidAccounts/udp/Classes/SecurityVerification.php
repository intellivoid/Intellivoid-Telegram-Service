<?php


    namespace udp\Classes;


    use Exception;
    use finfo;
    use udp\Abstracts\ImageType;
    use udp\Exceptions\FileUploadException;
    use udp\Exceptions\InvalidImageException;
    use udp\Exceptions\UnsupportedFileTypeException;
    use udp\Objects\ImageDetails;

    /**
     * Class SecurityVerification
     * @package udp\Classes
     */
    class SecurityVerification
    {
        /**
         * Checks the upload to see if it's valid
         *
         * @return bool
         * @throws FileUploadException
         * @throws UnsupportedFileTypeException
         */
        public static function verify_upload(): bool
        {
            if (!isset($_FILES['user_av_file']['error']) || is_array($_FILES['user_av_file']['error']))
            {
                throw new FileUploadException('Failed to process File Upload');
            }

            switch ($_FILES['user_av_file']['error'])
            {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new FileUploadException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new FileUploadException('Exceeded File Size limit.');
                default:
                    throw new FileUploadException('Unknown file upload error');
            }

            if ($_FILES['user_av_file']['size'] > 3145728)
            {
                throw new FileUploadException('Exceeded File Size limit.');
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $allowed_types = array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            );

            if (false === $ext = array_search($finfo->file($_FILES['user_av_file']['tmp_name']), $allowed_types, true))
            {
                throw new UnsupportedFileTypeException("The MIME type is unsupported");
            }

            return true;
        }

        /**
         * Verifies if the file is an actual image
         *
         * @param string $file
         * @return ImageDetails
         * @throws InvalidImageException
         * @throws UnsupportedFileTypeException
         */
        public static function verify_image(string $file): ImageDetails
        {
            try
            {
                $ImageSize = getimagesize($file);
            }
            catch(Exception $exception)
            {
                throw new InvalidImageException('Cannot process image size');
            }

            if(!$ImageSize)
            {
                throw new InvalidImageException('Cannot process image');
            }

            $valid_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);

            if(in_array($ImageSize[2],  $valid_types) == false)
            {
                throw new UnsupportedFileTypeException('The given file type is unsupported');
            }

            $ImageDetailsObject = new ImageDetails();
            $ImageDetailsObject->Width = $ImageSize[0];
            $ImageDetailsObject->Height = $ImageSize[1];

            if($ImageSize[2] == IMAGETYPE_JPEG)
            {
                $ImageDetailsObject->ImageType = ImageType::JPEG;
            }

            if($ImageSize[2] == IMAGETYPE_PNG)
            {
                $ImageDetailsObject->ImageType = ImageType::PNG;
            }

            return $ImageDetailsObject;

        }
    }