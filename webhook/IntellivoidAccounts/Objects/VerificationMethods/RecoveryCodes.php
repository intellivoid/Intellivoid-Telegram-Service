<?php


    namespace IntellivoidAccounts\Objects\VerificationMethods;


    use IntellivoidAccounts\Utilities\Hashing;
    use tsa\Exceptions\BadLengthException;
    use tsa\Exceptions\SecuredRandomProcessorNotFoundException;

    /**
     * Class RecoveryCodes
     * @package IntellivoidAccounts\Objects\VerificationMethods
     */
    class RecoveryCodes
    {
        /**
         * Indicates if the method is enabled or not
         *
         * @var bool
         */
        public $Enabled;

        /**
         * Array of available recovery codes
         *
         * @var array
         */
        public $RecoveryCodes;

        /**
         * The Unix Timestamp of when this was last updated
         *
         * @var int
         */
        public $LastUpdated;

        /**
         * Enables this method of verification and creates the recovery codes set
         *
         * @throws BadLengthException
         * @throws SecuredRandomProcessorNotFoundException
         */
        public function enable()
        {
            $this->Enabled = true;
            $this->RecoveryCodes = [];
            $this->LastUpdated = (int)time();

            while(true)
            {
                if(count($this->RecoveryCodes) > 12)
                {
                    break;
                }

                $this->RecoveryCodes[] = Hashing::recoveryCode();
            }
        }

        /**
         * Disables this method of verification and clears the recovery codes
         */
        public function disable()
        {
            $this->Enabled = false;
            $this->RecoveryCodes = [];
            $this->LastUpdated = 0;
        }


        /**
         * Verifies the given recovery code
         *
         * @param string $input
         * @param bool $remove_code
         * @return bool
         */
        public function verifyCode(string $input, bool $remove_code = false): bool
        {

            if($this->Enabled == false)
            {
                return False;
            }

            if(isset($this->RecoveryCodes[$input]) == false)
            {
                return False;
            }

            if($remove_code == true)
            {
                unset($this->RecoveryCodes[$input]);
            }

            return True;
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
                'recovery_codes' => $this->RecoveryCodes,
                'last_updated' => (int)$this->LastUpdated
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return RecoveryCodes
         */
        public static function fromArray(array $data): RecoveryCodes
        {
            $RecoveryCodesObject = new RecoveryCodes();

            if(isset($data['enabled']))
            {
                $RecoveryCodesObject->Enabled = (bool)$data['enabled'];
            }

            if(isset($data['recovery_codes']))
            {
                $RecoveryCodesObject->RecoveryCodes = $data['recovery_codes'];
            }

            if(isset($data['last_updated']))
            {
                $RecoveryCodesObject->LastUpdated = (int)$data['last_updated'];
            }

            return $RecoveryCodesObject;
        }
    }