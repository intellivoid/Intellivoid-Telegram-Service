<?php


    namespace IntellivoidAccounts\Objects\VerificationMethods;

    use IntellivoidAccounts\Utilities\Hashing;
    use tsa\Exceptions\BadLengthException;
    use tsa\Exceptions\SecuredRandomProcessorNotFoundException;

    /**
     * Class CurlVerification
     * @package IntellivoidAccounts\Objects\VerificationMethods
     */
    class CurlVerification
    {
        /**
         * Indicates if this method of verification is available
         *
         * @var bool
         */
        public $Enabled;

        /**
         * The Public Key without the account's hashed password combination
         *
         * @var string
         */
        public $PublicKey;

        /**
         * The magic key that calculates the Private Key
         *
         * @var string
         */
        public $MagicKey;

        /**
         * The current challenge that's set
         *
         * @var string|null
         */
        public $CurrentChallenge;

        /**
         * The Unix Timestamp of when this was last updated
         *
         * @var int
         */
        public $LastUpdated;

        /**
         * Calculates the private key
         *
         * @return string
         */
        public function calculatePrivateKey(): string
        {
            if($this->Enabled == false)
            {
                return null;
            }

            return Hashing::curlPrivateKey($this->PublicKey, $this->MagicKey);
        }

        /**
         * Creates a new Challenge
         *
         * @return string
         */
        public function createChallenge(): string
        {
            $this->CurrentChallenge = Hashing::curlCreateChallenge($this->MagicKey);
            return $this->CurrentChallenge;
        }

        /**
         * Verifies the Curl Challenge
         *
         * @param string $input
         * @return bool
         */
        public function verifyAnswer(string $input): bool
        {
            if($this->Enabled == false)
            {
                return false;
            }

            if($this->CurrentChallenge == false)
            {
                return false;
            }

            if($input == Hashing::curlChallengeAnswer($this->CurrentChallenge, self::calculatePrivateKey()))
            {
                return true;
            }

            return false;
        }

        /**
         * Creates the required keys and sets this method as enabled
         *
         * @throws BadLengthException
         * @throws SecuredRandomProcessorNotFoundException
         */
        public function Enable()
        {
            $this->Enabled = true;
            $this->MagicKey = Hashing::magicKey();
            $this->PublicKey = Hashing::curlPublicKey($this->MagicKey);
            $this->CurrentChallenge = null;
            $this->LastUpdated = time();
        }

        /**
         * Disables this method of verification
         */
        public function Disable()
        {
            $this->Enabled = false;
            $this->MagicKey = null;
            $this->PublicKey = null;
            $this->CurrentChallenge = null;
            $this->LastUpdated = 0;
        }

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'enabled' => $this->Enabled,
                'magic_key' => $this->MagicKey,
                'public_key' => $this->PublicKey,
                'current_challenge' => $this->CurrentChallenge,
                'last_updated' => $this->LastUpdated
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return CurlVerification
         */
        public static function fromArray(array $data): CurlVerification
        {
            $CurlVerificationObject = new CurlVerification();

            if(isset($data['enabled']))
            {
                $CurlVerificationObject->Enabled = (bool)$data['enabled'];
            }

            if(isset($data['magic_key']))
            {
                $CurlVerificationObject->MagicKey = $data['magic_key'];
            }

            if(isset($data['public_key']))
            {
                $CurlVerificationObject->PublicKey = $data['public_key'];
            }

            if(isset($data['current_challenge']))
            {
                $CurlVerificationObject->CurrentChallenge = $data['current_challenge'];
            }

            if(isset($data['last_updated']))
            {
                $CurlVerificationObject->LastUpdated = (int)$data['last_updated'];
            }

            return $CurlVerificationObject;
        }
    }