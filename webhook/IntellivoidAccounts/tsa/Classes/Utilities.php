<?php


    namespace tsa\Classes;


    use tsa\Exceptions\Base32DecodingException;

    /**
     * Utilities used by the crypto class
     *
     * Class Utilities
     * @package tsa\Classes
     */
    class Utilities
    {

        /**
         * Get array with all 32 characters for decoding from/encoding to base32.
         *
         * @return array
         */
        public static function getBase32LookupTable(): array
        {
            return array(
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
                'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
                'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
                'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
                '=',  // padding char
            );
        }

        /**
         * Decodes base32 data
         *
         * @param $data
         * @return string
         * @throws Base32DecodingException
         */
        public static function base32Decode($data): string
        {
            if (empty($data))
            {
                throw new Base32DecodingException();
            }

            $base32chars = Utilities::getBase32LookupTable();
            $base32charsFlipped = array_flip($base32chars);
            $paddingCharCount = substr_count($data, $base32chars[32]);
            $allowedValues = array(6, 4, 3, 1, 0);

            if (!in_array($paddingCharCount, $allowedValues))
            {
                throw new Base32DecodingException();
            }

            for ($i = 0; $i < 4; ++$i) {
                if ($paddingCharCount == $allowedValues[$i] &&
                    substr($data, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i]))
                {
                    throw new Base32DecodingException();
                }
            }

            $data = str_replace('=', '', $data);
            $data = str_split($data);
            $binaryString = '';

            for ($i = 0; $i < count($data); $i = $i + 8)
            {
                $x = '';

                if (!in_array($data[$i], $base32chars))
                {
                    throw new Base32DecodingException();
                }

                for ($j = 0; $j < 8; ++$j)
                {
                    $x .= str_pad(
                        base_convert(@$base32charsFlipped[@$data[$i + $j]], 10, 2),
                        5, '0', STR_PAD_LEFT
                    );
                }

                $eightBits = str_split($x, 8);

                for ($z = 0; $z < count($eightBits); ++$z)
                {
                    $binaryString .= (($y = chr(
                        base_convert($eightBits[$z], 2, 10))
                        ) || ord($y) == 48) ? $y : '';
                }
            }

            return $binaryString;
        }

        /**
         * A timing safe equals comparison
         *
         * @param $safeString
         * @param $userString
         * @return bool
         */
        public static function timingSafeEquals($safeString, $userString)
        {
            if (function_exists('hash_equals'))
            {
                return hash_equals($safeString, $userString);
            }

            $safeLen = strlen($safeString);
            $userLen = strlen($userString);
            if ($userLen != $safeLen)
            {
                return false;
            }

            $result = 0;
            for ($i = 0; $i < $userLen; ++$i)
            {
                $result |= (ord($safeString[$i]) ^ ord($userString[$i]));
            }

            // They are only identical strings if $result is exactly 0...
            return $result === 0;
        }

        /**
         * Creates the data for a QR code that can be scanned by the end user
         *
         * @param $name
         * @param $secret
         * @param null $title
         * @param array $params
         * @return string
         */
        public static function createQrCodeImage($name, $secret, $title = null, $params = array()): string
        {
            $width = !empty($params['width']) && (int) $params['width'] > 0 ? (int) $params['width'] : 200;
            $height = !empty($params['height']) && (int) $params['height'] > 0 ? (int) $params['height'] : 200;
            $level = !empty($params['level']) && array_search($params['level'], array('L', 'M', 'Q', 'H')) !== false ? $params['level'] : 'M';
            $urlencoded = urlencode('otpauth://totp/'.$name.'?secret='.$secret.'');

            if (isset($title))
            {
                $urlencoded .= urlencode('&issuer='.urlencode($title));
            }

            return "https://api.qrserver.com/v1/create-qr-code/?data=$urlencoded&size=${width}x${height}&ecc=$level";

        }

    }