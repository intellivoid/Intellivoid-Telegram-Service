<?php


    namespace tsa\Classes;


    use Exception;
    use tsa\Exceptions\BadLengthException;
    use tsa\Exceptions\Base32DecodingException;
    use tsa\Exceptions\InvalidSecretException;
    use tsa\Exceptions\SecuredRandomProcessorNotFoundException;

    /**
     * Crypto Functions
     *
     * Class Crypto
     * @package tsa\Classes
     */
    class Crypto
    {
        /**
         * Creates a new secret key
         *
         * @param int $secretLength
         * @return string
         * @throws BadLengthException
         * @throws SecuredRandomProcessorNotFoundException
         * @throws Exception
         */
        public static function BuildSecretSignature($secretLength = 16): string
        {
            $validChars = Utilities::getBase32LookupTable();

            // Check if the length are valid
            if ($secretLength < 16 || $secretLength > 128)
            {
                throw new BadLengthException();
            }

            $secret = '';
            $rnd = false;
            if (function_exists('random_bytes'))
            {
                $rnd = random_bytes($secretLength);
            }
            elseif (function_exists('openssl_random_pseudo_bytes'))
            {
                $rnd = openssl_random_pseudo_bytes($secretLength, $cryptoStrong);
                if (!$cryptoStrong)
                {
                    $rnd = false;
                }
            }
            elseif (function_exists('mcrypt_create_iv'))
            {
                $rnd = mcrypt_create_iv($secretLength, MCRYPT_DEV_URANDOM);
            }

            if ($rnd !== false)
            {
                for ($i = 0; $i < $secretLength; ++$i) {
                    $secret .= $validChars[ord($rnd[$i]) & 31];
                }
            }
            else
            {
                throw new SecuredRandomProcessorNotFoundException();
            }

            return $secret;
        }

        /**
         * Generates a code from the secret signature
         *
         * @param $secret_signature
         * @param null $timeSlice
         * @return string
         * @throws InvalidSecretException
         */
        public static function getCode($secret_signature, $timeSlice = null): string
        {
            if ($timeSlice === null)
            {
                $timeSlice = floor(time() / 30);
            }

            try
            {
                $secretkey = Utilities::base32Decode($secret_signature);
            }
            catch(Base32DecodingException $base32DecodingException)
            {
                throw new InvalidSecretException();
            }

            // Pack time into binary string
            $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);

            // Hash it with users secret key
            $hm = hash_hmac('SHA1', $time, $secretkey, true);

            // Use last nipple of result as index/offset
            $offset = ord(substr($hm, -1)) & 0x0F;

            // grab 4 bytes of the result
            $hashpart = substr($hm, $offset, 4);

            // Unpak binary value
            $value = unpack('N', $hashpart);
            $value = $value[1];

            // Only 32 bits
            $value = $value & 0x7FFFFFFF;
            $modulo = pow(10, 6);

            return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
        }


        /**
         * Verifies the given code with the secret signature
         *
         * @param $secret_signature
         * @param $code
         * @param int $discrepancy
         * @param null $currentTimeSlice
         * @return bool
         */
        public static function verifyCode($secret_signature, $code, $discrepancy = 1, $currentTimeSlice = null): bool
        {
            if ($currentTimeSlice === null)
            {
                $currentTimeSlice = floor(time() / 30);
            }

            if (strlen($code) != 6)
            {
                return false;
            }

            for ($i = -$discrepancy; $i <= $discrepancy; ++$i)
            {
                try
                {
                    $calculatedCode = Crypto::getCode($secret_signature, $currentTimeSlice + $i);
                }
                catch(InvalidSecretException $invalidSecretException)
                {
                    return false;
                }

                if (Utilities::timingSafeEquals($calculatedCode, $code))
                {
                    return true;
                }
            }

            return false;
        }


    }