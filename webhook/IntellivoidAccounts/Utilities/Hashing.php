<?php

    namespace IntellivoidAccounts\Utilities;

    use IntellivoidAccounts\Objects\COA\AuthenticationRequest;
    use tsa\Classes\Crypto;
    use tsa\Exceptions\BadLengthException;
    use tsa\Exceptions\SecuredRandomProcessorNotFoundException;

    /**
     * Class Hashing
     * @package IntellivoidAccounts\Utilities
     */
    class Hashing
    {
        /**
         * Peppers a hash using whirlpool
         *
         * @param string $Data The hash to pepper
         * @param int $Min Minimal amounts of executions
         * @param int $Max Maximum amount of executions
         * @return string
         */
        public static function pepper(string $Data, int $Min = 100, int $Max = 1000): string
        {
            $n = rand($Min, $Max);
            $res = '';
            $Data = hash('whirlpool', $Data);
            for ($i=0,$l=strlen($Data) ; $l ; $l--)
            {
                $i = ($i+$n-1) % $l;
                $res = $res . $Data[$i];
                $Data = ($i ? substr($Data, 0, $i) : '') . ($i < $l-1 ? substr($Data, $i+1) : '');
            }
            return($res);
        }

        /**
         * Calculates the Public ID of the Account
         *
         * @param string $username
         * @param string $password
         * @param string $email
         * @return string
         */
        public static function publicID(string $username, string $password, string $email): string
        {
            $username = hash('haval256,3', $username);
            $password = hash('haval192,4', $password);
            $email = hash('haval256,5', $email);

            $crc_2 = hash('haval160,3', $username . $email);
            $crc_3 = hash('haval128,3', $username . $password);

            return hash('ripemd320', $crc_2 . $crc_3);
        }

        /**
         * Hashes the password
         *
         * @param string $password
         * @return string
         */
        public static function password(string $password)
        {
            return hash('sha512', $password) .  hash('haval256,5', $password);
        }

        /**
         * Creates a public ID for a login record
         *
         * @param int $account_id
         * @param int $unix_timestamp
         * @param int $status
         * @param string $origin
         * @return string
         */
        public static function loginPublicID(int $account_id, int $unix_timestamp, int $status, string $origin)
        {
            $account_id = hash('haval256,5', $account_id);
            $unix_timestamp = hash('haval256,5', $unix_timestamp);
            $status = hash('haval256,5', $status);
            $origin = hash('haval256,5', $origin);

            $crc1 = hash('sha256', $account_id . $unix_timestamp . $status);
            $crc2 = hash('sha256', $origin . $crc1);

            return $crc1 . $crc2;
        }

        /**
         * Creates a public ID for a balance transaction record
         *
         * @param int $account_id
         * @param int $unix_timestamp
         * @param int $amount
         * @param string $source
         * @return string
         */
        public static function balanceTransactionPublicID(int $account_id, int $unix_timestamp, int $amount, string $source): string
        {
            $builder = self::pepper($source);
            $builder .= hash('crc32', $account_id);
            $builder .= hash('crc32', $unix_timestamp);
            $builder .= hash('crc32', $amount);
            $builder .= hash('crc32', $source);

            return $builder;
        }

        /**
         * Creates a transaction public record ID
         *
         * @param int $account_id
         * @param int $unix_timestamp
         * @param float $amount
         * @param string $vendor
         * @param int $operator_type
         * @return string
         */
        public static function transactionRecordPublicID(int $account_id, int $unix_timestamp, float $amount, string $vendor, int $operator_type): string
        {
            $builder = self::pepper($vendor);

            $builder .= hash('crc32', $account_id);
            $builder .= hash('crc32', $unix_timestamp - 100);
            $builder .= hash('crc32', $amount + 200);
            $builder .= hash('crc32', $operator_type + 5);

            return $builder;
        }

        /**
         * Creates a Public ID for a known host record
         *
         * @param string $ip_address
         * @param string $user_agent
         * @param int $unix_timestamp
         * @return string
         */
        public static function knownHostPublicID(string $ip_address, string $user_agent, int $unix_timestamp): string
        {
            $builder = self::pepper($ip_address);

            $builder .= hash('haval256,5', $ip_address);
            $builder .= hash('crc32', $user_agent . $builder);
            $builder .= hash('crc32', $unix_timestamp . $builder);

            return $builder;
        }

        /**
         * Creates a magic key that can be use to calculate the client side
         *
         * @return string
         * @throws BadLengthException
         * @throws SecuredRandomProcessorNotFoundException
         */
        public static function magicKey(): string
        {
            $builder = self::pepper(hash('sha256', time())) . self::recoveryCode();
            return hash('haval256,5', $builder . self::recoveryCode());
        }

        /**
         * Calculates the public key for cURL
         *
         * @param string $magic_key
         * @return string
         */
        public static function curlPublicKey(string $magic_key): string
        {
            return self::pepper($magic_key) . "-intellivoid-iauth";
        }

        /**
         * Creates a unique cURL Challenge
         *
         * @param string $magic_key
         * @return string
         */
        public static function curlCreateChallenge(string $magic_key): string
        {
            return self::pepper($magic_key) .  hash('sha256', self::pepper($magic_key) . time());
        }

        /**
         * Calculates the answer to the challenge
         *
         * @param string $challenge
         * @param $private_key
         * @return string
         */
        public static function curlChallengeAnswer(string $challenge, $private_key): string
        {
            return hash('sha256', $challenge . $private_key);
        }

        /**
         * Calculates the private key for cURL
         *
         * @param string $public_key
         * @param string $magic_key
         * @return string
         */
        public static function curlPrivateKey(string $public_key, string $magic_key): string
        {
            return hash('sha256', $public_key . '-' . $magic_key);
        }

        /**
         * Creates a new Message Public ID
         *
         * @param int $from_id
         * @param int $to_id
         * @param int $unix_timestamp
         * @return string
         */
        public static function messagePublicID(int $from_id, int $to_id, int $unix_timestamp): string
        {
            $builder = "M-";

            $builder .= hash("crc32", $from_id);
            $builder .= hash("crc32", $to_id);
            $builder .= hash("crc32", $unix_timestamp);
            $builder .= self::pepper($builder);

            return $builder;
        }

        /**
         * Creates a random but secured recovery code
         *
         * @return string
         * @throws BadLengthException
         * @throws SecuredRandomProcessorNotFoundException
         */
        public static function recoveryCode(): string
        {
            return hash('adler32', self::pepper(Crypto::BuildSecretSignature(16) . time()));
        }

        /**
         * Creates a unique public telegram client ID
         *
         * @param string $chat_id
         * @param int $user_id
         * @return string
         */
        public static function telegramClientPublicID(string $chat_id, int $user_id): string
        {
            $builder = "TEL-";

            $builder .= hash('sha256', $chat_id);
            $builder .= '-' . hash('crc32', $user_id);

            return $builder;
        }

        /**
         * Creates a unique public id for the application
         *
         * @param string $name
         * @param int $timestamp
         * @return string
         */
        public static function applicationPublicId(string $name, int $timestamp): string
        {
            $builder = "APP";
            $builder .= hash('sha256', $name . $timestamp);
            $builder .= hash('crc32', $timestamp);
            return $builder;
        }

        /**
         * Builds a secret key for the application
         *
         * @param string $public_key
         * @param int $timestamp
         * @return string
         */
        public static function applicationSecretKey(string $public_key, int $timestamp): string
        {
            return hash('sha256', self::pepper($public_key) . $timestamp) . hash('crc32', self::pepper($timestamp));
        }

        /**
         * Creates an authentication request token
         *
         * @param int $application_id
         * @param string $application_name
         * @param int $host_id
         * @param int $timestamp
         * @return string
         */
        public static function authenticationRequestToken(int $application_id, string $application_name, int $host_id, int $timestamp): string
        {
            $application_id = hash('crc32', $application_id);
            $application_name = hash('crc32', $application_name);
            $host_id = hash('crc32', $host_id);
            $timestamp = hash('crc32', $timestamp);

            $hash = hash('sha256', $application_id . $application_name . $host_id . $timestamp);
            $ending = hash('crc32', self::pepper($hash));

            return $hash . $ending;
        }

        /**
         * Creates an Authentication Access Token
         *
         * @param int $request_id
         * @param string $request_token
         * @param int $timestamp
         * @param int $account_id
         * @param int $host_id
         * @return string
         */
        public static function authenticationAccessToken(int $request_id, string $request_token, int $timestamp, int $account_id, int $host_id): string
        {
            $request_id = hash('crc32', $request_id);
            $request_token = self::pepper($request_token);
            $timestamp = hash('crc32', $timestamp);
            $account_id = hash('crc32', $account_id);
            $host_id = hash('crc32', $host_id);

            return hash('sha256', $request_id . $request_token . $timestamp . $account_id . $host_id);
        }

        /**
         * Generates a Telegram Verifrication Code
         *
         * @param int $telegram_client_id
         * @param int $timestamp
         * @return string
         */
        public static function telegramVerificationCode(int $telegram_client_id, int $timestamp): string
        {
            $telegram_client_id = hash('crc32', $telegram_client_id);
            $timestamp = hash('sha256', $timestamp);

            return hash('sha256', $telegram_client_id . $timestamp);
        }

        /**
         * Calculates the tracking ID from the user_agent_string and host_id
         *
         * @param string $user_agent_string
         * @param int $host_id
         * @return string
         */
        public static function uaTrackingId(string $user_agent_string, int $host_id): string
        {
            return hash ('sha256', $user_agent_string . $host_id);
        }

        /**
         * Builds a unique, one-time login code used for authentication
         *
         * @param int $account_id
         * @param int $timestamp
         * @param int $expires
         * @return string
         */
        public static function OneTimeLoginCode(int $account_id, int $timestamp, int $expires): string
        {
            $account = hash('sha256', $account_id);
            $timestamp = hash('sha256', $timestamp . $expires);
            $expires = hash('sha256', $expires . $timestamp);

            $seed = hash('adler32', self::pepper($account));
            $timestamp_arc = hash('crc32b', $account . $timestamp);
            $expires_arc = hash('crc32b', $account . $expires);

            return $timestamp_arc . $expires_arc . hash('crc32b', $timestamp_arc . $expires_arc . $seed);
        }

        /**
         * Calculates the public ID for the
         *
         * @param int $account_id
         * @param int $application_id
         * @param int $timestamp
         * @return string
         */
        public static function ApplicationAccess(int $account_id, int $application_id): string
        {
            $account_id_c = hash('sha256', $account_id  . 'ACCOUNT');
            $application_id_c = hash('sha256', $application_id . 'APPLICATION');

            $core = hash('sha256', $account_id_c . $application_id_c . 'C');
            return $core . hash('crc32b', $account_id_c) . hash('crc32b', $application_id_c);
        }
    }