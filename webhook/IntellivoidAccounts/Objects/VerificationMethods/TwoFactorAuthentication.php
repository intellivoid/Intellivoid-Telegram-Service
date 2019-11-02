<?php


    namespace IntellivoidAccounts\Objects\VerificationMethods;


    use tsa\Classes\Crypto;
    use tsa\Exceptions\BadLengthException;
    use tsa\Exceptions\InvalidSecretException;
    use tsa\Exceptions\SecuredRandomProcessorNotFoundException;

    /**
     * Two Factor Authentication Method for Account Security
     *
     * Class TwoFactorAuthentication
     * @package IntellivoidAccounts\Objects\VerificationMethods
     */
    class TwoFactorAuthentication
    {
        /**
         * Indicates if this method is enabled or not
         *
         * @var bool
         */
        public $Enabled;

        /**
         * The Unix Timestamp of when the signature was last updated
         *
         * @var int
         */
        public $LastUpdated;

        /**
         * The private signature associated with this account
         *
         * @var string
         */
        public $PrivateSignature;

        /**
         * Generates a new Private Signature and enables this
         * method of authentication
         *
         * @throws BadLengthException
         * @throws SecuredRandomProcessorNotFoundException
         */
        public function enable()
        {
            $this->Enabled = true;
            $this->PrivateSignature = Crypto::BuildSecretSignature(16);
            $this->LastUpdated = time();
        }

        /**
         * Disables this method of authentication and deletes the private
         * signature
         */
        public function disable()
        {
            $this->Enabled = false;
            $this->PrivateSignature = null;
            $this->LastUpdated = 0;
        }

        /**
         * Returns the current verification code
         *
         * @return string
         * @throws InvalidSecretException
         */
        public function currentCode(): string
        {
            if($this->Enabled == false)
            {
                return null;
            }

            return Crypto::getCode($this->PrivateSignature);
        }

        /**
         * Determines if the input code is verified and correct
         *
         * @param string $input
         * @return bool
         */
        public function verifyCode(string $input): bool
        {
            if($this->Enabled == false)
            {
                return False;
            }

            try
            {
                if(Crypto::verifyCode($this->PrivateSignature, $input) == true)
                {
                    return True;
                }
            }
            catch(\Exception $exception)
            {
                return False;
            }

            return False;
        }

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'enabled' => (bool)$this->Enabled,
                'last_updated' => (int)$this->LastUpdated,
                'private_signature' => $this->PrivateSignature
            );
        }

        /**
         * Returns an object from a array
         *
         * @param array $data
         * @return TwoFactorAuthentication
         */
        public static function fromArray(array $data): TwoFactorAuthentication
        {
            $TwoFactorAuthenticationObject = new TwoFactorAuthentication();

            if(isset($data['enabled']))
            {
                $TwoFactorAuthenticationObject->Enabled = (bool)$data['enabled'];
            }

            if(isset($data['last_updated']))
            {
                $TwoFactorAuthenticationObject->LastUpdated = (int)$data['last_updated'];
            }

            if(isset($data['private_signature']))
            {
                $TwoFactorAuthenticationObject->PrivateSignature = $data['private_signature'];
            }

            return $TwoFactorAuthenticationObject;
        }
    }