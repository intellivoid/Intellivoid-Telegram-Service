<?php

    namespace IntellivoidAccounts\Utilities;

    use IntellivoidAccounts\Abstracts\AccountRequestPermissions;
    use IntellivoidAccounts\Abstracts\ApplicationFlags;

    /**
     * Class Validate
     * @package IntellivoidAccounts\Utilities
     */
    class Validate
    {
        /**
         * Determines if the given username is valid
         *
         * Must be alphanumeric, greater than 5 characters but no greater than 64
         *
         * @param string $input
         * @return bool
         */
        public static function username(string $input): bool
        {
            if(!preg_match('/^[a-zA-Z0-9]{5,}$/', $input))
            {
                return false;
            }

            if(strlen($input) > 64)
            {
                return false;
            }

            return true;
        }

        /**
         * Determines if the password is valid
         *
         * must be greater than 8 characters but no greater than 128
         *
         * @param string $input
         * @return bool
         */
        public static function password(string $input): bool
        {
            if(strlen($input) < 8)
            {
                return false;
            }

            if(strlen($input) > 128)
            {
                return false;
            }

            return true;
        }

        /**
         * Determines if the email is valid
         *
         * @param string $input
         * @return bool
         */
        public static function email(string $input): bool
        {
            if(!filter_var($input, FILTER_VALIDATE_EMAIL))
            {
                return false;
            }

            if(strlen($input) > 128)
            {
                return false;
            }

            return true;
        }

        /**
         * Validates if the given IP is ipv4 and or ipv6 valid
         *
         * @param string $ip_address
         * @return bool
         */
        public static function ip(string $ip_address): bool
        {
            if(filter_var($ip_address, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) == true)
            {
                return true;
            }

            if(filter_var($ip_address, FILTER_VALIDATE_IP,FILTER_FLAG_IPV6) == true)
            {
                return true;
            }

            return false;
        }

        /**
         * Validates if a vendor's name is valid or not
         *
         * @param string $input
         * @return bool
         */
        public static function vendor(string $input): bool
        {
            if(strlen($input) > 200)
            {
                return false;
            }

            if(strlen($input) < 3)
            {
                return false;
            }

            return true;
        }

        /**
         * Determines if the user agent is valid
         *
         * @param string $input
         * @return bool
         */
        public static function userAgent(string $input): bool
        {
            if(strlen($input) < 3)
            {
                return false;
            }

            if(strlen($input) > 526)
            {
                return false;
            }

            return true;
        }

        /**
         * Verifies if the given password and the hashed password matches
         *
         * @param string $password
         * @param string $hash
         * @return bool
         */
        public static function verifyHashedPassword(string $password, string $hash): bool
        {
            if($hash == Hashing::password($password))
            {
                return true;
            }

            return false;
        }

        /**
         * Verifies if the given permission is valid
         *
         * @param string $permission
         * @return bool
         */
        public static function verify_permission(string $permission): bool
        {
            switch($permission)
            {
                case AccountRequestPermissions::ReadPersonalInformation:
                case AccountRequestPermissions::EditPersonalInformation:
                case AccountRequestPermissions::ViewEmailAddress:
                case AccountRequestPermissions::MakePurchases:
                case AccountRequestPermissions::TelegramNotifications:
                    return true;
                default:
                    return false;
            }
        }

        /**
         * Verifies if the given Application Flag is valid
         *
         * @param string $flag
         * @return bool
         */
        public static function verify_application_flag(string $flag): bool
        {
            switch($flag)
            {
                case ApplicationFlags::Official:
                case ApplicationFlags::Untrusted:
                case ApplicationFlags::Verified:
                    return true;
                default:
                    return false;
            }
        }

        /**
         * Validates an Application Name
         *
         * @param string $input
         * @return bool
         */
        public static function applicationName(string $input): bool
        {
            if(strlen($input) > 120)
            {
                return false;
            }

            if(strlen($input) < 3)
            {
                return false;
            }

            if(preg_match("/^[a-zA-Z0-9 ]*$/", $input))
            {
                return true;
            }

            return false;
        }

        /**
         * Validates if the first name is valid
         *
         * @param string $input
         * @return bool
         */
        public static function firstName(string $input): bool
        {
            if(strlen($input) < 1)
            {
                return false;
            }

            if(strlen($input) > 50)
            {
                return false;
            }

            if(!preg_match("/^([a-zA-Z' ]+)$/", $input))
            {
                return false;
            }

            return true;
        }

        /**
         * Validates if the last name is valid
         *
         * @param string $input
         * @return bool
         */
        public static function lastName(string $input): bool
        {
            if(strlen($input) < 1)
            {
                return false;
            }
            
            if(strlen($input) > 50)
            {
                return false;
            }

            if(!preg_match("/^([a-zA-Z' ]+)$/", $input))
            {
                return false;
            }

            return true;
        }

        /**
         * Validates if a URL is valid or not
         *
         * @param string $input
         * @return bool
         */
        public static function url(string $input): bool
        {
            if(filter_var($input, FILTER_VALIDATE_URL) == false)
            {
                return false;
            }

            return true;
        }

        /**
         * Validates if the subscription plan name is valid
         *
         * @param string $input
         * @return bool
         */
        public static function subscriptionPlanName(string $input): bool
        {
            if(strlen($input) > 120)
            {
                return false;
            }

            if(strlen($input) < 3)
            {
                return false;
            }

            if(preg_match("/^[a-zA-Z0-9 ]*$/", $input))
            {
                return true;
            }

            return false;
        }

        /**
         * Validates if the promotion code for the subscription is valid or not
         *
         * @param string $input
         * @return bool
         */
        public static function subscriptionPromotionCode(string $input): bool
        {
            if(strlen($input) > 120)
            {
                return false;
            }

            if(strlen($input) < 3)
            {
                return false;
            }

            if(preg_match("/^[a-zA-Z0-9 ]*$/", $input))
            {
                return true;
            }

            return false;
        }
    }