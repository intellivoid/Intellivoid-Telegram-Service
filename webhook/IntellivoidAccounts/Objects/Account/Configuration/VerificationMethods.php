<?php


    namespace IntellivoidAccounts\Objects\Account\Configuration;

    use IntellivoidAccounts\Objects\VerificationMethods\RecoveryCodes;
    use IntellivoidAccounts\Objects\VerificationMethods\TelegramLink;
    use IntellivoidAccounts\Objects\VerificationMethods\TwoFactorAuthentication;

    /**
     * Verification methods that this account uses
     *
     * Class VerificationMethods
     * @package IntellivoidAccounts\Objects\Account\Configuration
     */
    class VerificationMethods
    {

        /**
         * Indicates if TwoFactorAuthentication is enabled on this account
         *
         * @var bool
         */
        public $TwoFactorAuthenticationEnabled;

        /**
         * TwoFactorAuthentication Configuration
         *
         * @var TwoFactorAuthentication
         */
        public $TwoFactorAuthentication;

        /**
         * Indicates if RecoveryCodes are enabled on this account
         *
         * @var bool
         */
        public $RecoveryCodesEnabled;

        /**
         * RecoveryCodes Configuration
         *
         * @var RecoveryCodes
         */
        public $RecoveryCodes;

        /**
         * Indicates if a Telegram Client has been linked which allows IV to send notifications to said client
         *
         * @var bool
         */
        public $TelegramClientLinked;

        /**
         * TelegramLink Configuration
         *
         * @var TelegramLink
         */
        public $TelegramLink;

        /**
         * VerificationMethods constructor.
         */
        public function __construct()
        {
            $this->TwoFactorAuthentication = new TwoFactorAuthentication();
            $this->RecoveryCodes =  new RecoveryCodes();
            $this->TelegramLink = new TelegramLink();
        }

        /**
         * Creates array from object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                '2fa_auth_enabled' => (bool)$this->TwoFactorAuthenticationEnabled,
                '2fa_auth' => $this->TwoFactorAuthentication->toArray(),
                'recovery_codes_enabled' => (bool)$this->RecoveryCodesEnabled,
                'recovery_codes' => $this->RecoveryCodes->toArray(),
                'telegram_linked' => (bool)$this->TelegramClientLinked,
                'telegram_link' => $this->TelegramLink->toArray()
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return VerificationMethods
         */
        public static function fromArray(array $data): VerificationMethods
        {
            $VerificationMethodsObject = new VerificationMethods();

            if(isset($data['2fa_auth_enabled']) == false)
            {
                $VerificationMethodsObject->TwoFactorAuthenticationEnabled = false;
            }
            else
            {
                $VerificationMethodsObject->TwoFactorAuthenticationEnabled = (bool)$data['2fa_auth_enabled'];
            }

            if(isset($data['2fa_auth']) == false)
            {
                $VerificationMethodsObject->TwoFactorAuthentication = new TwoFactorAuthentication();
                $VerificationMethodsObject->TwoFactorAuthentication->disable();
            }
            else
            {
                $VerificationMethodsObject->TwoFactorAuthentication = TwoFactorAuthentication::fromArray($data['2fa_auth']);
            }

            if(isset($data['recovery_codes_enabled']) == false)
            {
                $VerificationMethodsObject->RecoveryCodesEnabled = false;
            }
            else
            {
                $VerificationMethodsObject->RecoveryCodesEnabled = (bool)$data['recovery_codes_enabled'];
            }

            if(isset($data['recovery_codes']) == false)
            {
                $VerificationMethodsObject->RecoveryCodes = new RecoveryCodes();
                $VerificationMethodsObject->RecoveryCodes->disable();
            }
            else
            {
                $VerificationMethodsObject->RecoveryCodes = RecoveryCodes::fromArray($data['recovery_codes']);
            }

            if(isset($data['telegram_linked']) == false)
            {
                $VerificationMethodsObject->TelegramClientLinked = false;
            }
            else
            {
                $VerificationMethodsObject->TelegramClientLinked = (bool)$data['telegram_linked'];
            }

            if(isset($data['telegram_link']) == false)
            {
                $VerificationMethodsObject->TelegramLink = new TelegramLink();
                $VerificationMethodsObject->TelegramLink->disable();
            }
            else
            {
                $VerificationMethodsObject->TelegramLink = TelegramLink::fromArray($data['telegram_link']);
            }

            return $VerificationMethodsObject;
        }
    }