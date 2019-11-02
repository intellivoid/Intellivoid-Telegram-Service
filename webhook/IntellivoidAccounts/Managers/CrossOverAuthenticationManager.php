<?php


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\IntellivoidAccounts;

    /**
     * Class CrossOverAuthenticationManager
     * @package IntellivoidAccounts\Managers
     */
    class CrossOverAuthenticationManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * @var AuthenticationRequestManager
         */
        private $authenticationRequestManager;

        /**
         * @var AuthenticationAccessManager
         */
        private $authenticationAccessManager;

        /**
         * @var ApplicationAccessManager
         */
        private $ApplicationAccessManager;

        /**
         * CrossOverAuthenticationManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
            $this->authenticationRequestManager = new AuthenticationRequestManager($intellivoidAccounts);
            $this->authenticationAccessManager = new AuthenticationAccessManager($intellivoidAccounts);
            $this->ApplicationAccessManager = new ApplicationAccessManager($intellivoidAccounts);
        }

        /**
         * @return AuthenticationRequestManager
         */
        public function getAuthenticationRequestManager(): AuthenticationRequestManager
        {
            return $this->authenticationRequestManager;
        }

        /**
         * @return AuthenticationAccessManager
         */
        public function getAuthenticationAccessManager(): AuthenticationAccessManager
        {
            return $this->authenticationAccessManager;
        }

        /**
         * @return ApplicationAccessManager
         */
        public function getApplicationAccessManager(): ApplicationAccessManager
        {
            return $this->ApplicationAccessManager;
        }
    }