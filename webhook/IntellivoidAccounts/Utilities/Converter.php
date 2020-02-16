<?php


    namespace IntellivoidAccounts\Utilities;


    use IntellivoidAccounts\Exceptions\InvalidSubscriptionPlanNameException;

    /**
     * Class Converter
     * @package IntellivoidAccounts\Utilities
     */
    class Converter
    {
        /**
         * Converts promotion code to standard promotion
         *
         * @param string $input
         * @return string
         */
        public static function subscriptionPromotionCode(string $input): string
        {
            $input = strtoupper($input);
            $input = str_ireplace(' ', '_', $input);
            return $input;
        }
    }