<?php


    namespace udp\Classes;


    /**
     * Class ImageProcessor
     * @package udp\Classes
     */
    class ImageProcessor
    {
        /**
         * Resizes the image
         *
         * @param $file
         * @param $w
         * @param $h
         * @param bool $crop
         * @return string
         */
        public static function resize_image($file, $w, $h, $crop = false): string
        {
            list($width, $height) = getimagesize($file);
            $r = $width / $height;

            if ($crop)
            {
                if ($width > $height)
                {
                    $width = ceil($width-($width*abs($r-$w/$h)));
                }
                else
                {
                    $height = ceil($height-($height*abs($r-$w/$h)));
                }
                $newwidth = $w;
                $newheight = $h;
            }
            else
            {
                if ($w/$h > $r)
                {
                    $newwidth = $h*$r;
                    $newheight = $h;
                }
                else
                {
                    $newheight = $w/$r;
                    $newwidth = $w;
                }
            }

            $src = imagecreatefromjpeg($file);
            $dst = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagejpeg($dst, $file);
            imagedestroy($dst);

            return $file;
        }

        /**
         * Converts PNG to JPEG, saves it as the same file name but with .jpg at the end instead of .png
         *
         * @param string $file
         * @return string
         */
        public static function convert(string $file): string
        {
            $image = imagecreatefrompng($file);
            $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagealphablending($bg, true);
            imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            imagejpeg($bg, $file, 100);
            imagedestroy($bg);

            return $file;
        }
    }