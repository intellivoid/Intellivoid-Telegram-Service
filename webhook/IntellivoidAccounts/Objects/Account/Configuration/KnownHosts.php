<?php


    namespace IntellivoidAccounts\Objects\Account\Configuration;

    /**
     * Class KnownHosts
     * @package IntellivoidAccounts\Objects\Account\Configuration
     */
    class KnownHosts
    {
        /**
         * Array of known host IDs
         *
         * @var array
         */
        public $KnownHosts;

        /**
         * Adds a known host id to be associated with this account
         *
         * @param int $id
         * @return bool Returns false when the host already exists
         */
        public function addHostId(int $id): bool
        {
            if($this->KnownHosts == null)
            {
                $this->KnownHosts = [];
            }

            if(isset($this->KnownHosts[$id]) == false)
            {
                $this->KnownHosts[] = $id;
                return true;
            }

            return false;
        }

        /**
         * Removes a known host id from being associated with this account
         *
         * @param int $id
         * @return bool Returns false when the host does not exist
         */
        public function removeHostId(int $id): bool
        {
            if($this->KnownHosts == null)
            {
                $this->KnownHosts = [];
            }

            if(isset($this->KnownHosts[$id]) == false)
            {
                return false;
            }

            unset($this->KnownHosts[$id]);
            return true;
        }

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            if($this->KnownHosts == null)
            {
                return [];
            }

            return $this->KnownHosts;
        }

        /**
         * Returns an object from an array structure
         *
         * @param array $data
         * @return KnownHosts
         */
        public static function fromArray(array $data): KnownHosts
        {
            $KnownHostsObject = new KnownHosts();
            $KnownHostsObject->KnownHosts = $data;
            return $KnownHostsObject;
        }
    }