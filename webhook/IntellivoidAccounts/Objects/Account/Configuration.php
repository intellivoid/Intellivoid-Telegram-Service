<?php

    namespace IntellivoidAccounts\Objects\Account;

    use IntellivoidAccounts\Objects\Account\Configuration\KnownHosts;
    use IntellivoidAccounts\Objects\Account\Configuration\Roles;
    use IntellivoidAccounts\Objects\Account\Configuration\VerificationMethods;

    /**
     * Class Configuration
     * @package IntellivoidAccounts\Objects\Account
     */
    class Configuration
    {

        /**
         * Known hosts associated with this account
         *
         * @var KnownHosts
         */
        public $KnownHosts;

        /**
         * Verification Methods that are available for this account
         *
         * @var VerificationMethods
         */
        public $VerificationMethods;

        /**
         * Roles associated with this account
         *
         * @var Roles
         */
        public $Roles;

        /**
         * The current balance in the account
         *
         * @var float
         */
        public $Balance;

        /**
         * Configuration constructor.
         */
        public function __construct()
        {
            $this->Balance = 0;
            $this->VerificationMethods = new VerificationMethods();
            $this->KnownHosts = new KnownHosts();
            $this->Roles = new Roles();
        }

        /**
         * Converts object to array
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'balance' => (float)$this->Balance,
                'verification_methods' => $this->VerificationMethods->toArray(),
                'known_hosts' => $this->KnownHosts->toArray(),
                'roles' => $this->Roles->toArray()
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Configuration
         */
        public static function fromArray(array $data): Configuration
        {
            $ConfigurationObject = new Configuration();

            if(isset($data['balance']))
            {
                $ConfigurationObject->Balance = (float)$data['balance'];
            }

            if(isset($data['verification_methods']))
            {
                $ConfigurationObject->VerificationMethods = VerificationMethods::fromArray($data['verification_methods']);
            }
            else
            {
                $ConfigurationObject->VerificationMethods = VerificationMethods::fromArray(array());
            }

            if(isset($data['known_hosts']))
            {
                $ConfigurationObject->KnownHosts = KnownHosts::fromArray($data['known_hosts']);
            }
            else
            {
                $ConfigurationObject->KnownHosts = new KnownHosts();
                $ConfigurationObject->KnownHosts->KnownHosts = [];
            }

            if(isset($data['roles']))
            {
                $ConfigurationObject->Roles = Roles::fromArray($data['roles']);
            }
            else
            {
                $ConfigurationObject->Roles = new Roles();
                $ConfigurationObject->Roles->Roles = [];
            }

            return $ConfigurationObject;
        }
    }