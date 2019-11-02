<?php


    namespace IntellivoidAccounts\Objects\COA;

    /**
     * Class AuthenticationAccess
     * @package IntellivoidAccounts\Objects\COA
     */
    class AuthenticationAccess
    {
        /**
         * The ID of the authentication access
         *
         * @var int
         */
        public $ID;

        /**
         * The access token used to retrieve information about the authenticated account
         *
         * @var string
         */
        public $AccessToken;

        /**
         * The ID of the application that issued this authentication access
         *
         * @var int
         */
        public $ApplicationId;

        /**
         * The id of the account that's authenticated
         *
         * @var int
         */
        public $AccountId;

        /**
         * The id of the authentication request that created this authentication access
         *
         * @var int
         */
        public $RequestId;

        /**
         * The permissions this Authentication Access has access to
         *
         * @var array
         */
        public $Permissions;

        /**
         * The current status of the access
         *
         * @var int
         */
        public $Status;

        /**
         * The Unix Timestamp of when this access expires
         *
         * @var int
         */
        public $ExpiresTimestamp;

        /**
         * The Unix Timestamp of when this record was last used
         *
         * @var int
         */
        public $LastUsedTimestamp;

        /**
         * The Unix Timestamp of when this record was created
         *
         * @var int
         */
        public $CreatedTimestamp;

        /**
         * Determines if the Authentication Access has the specified permission
         *
         * @param string $permission
         * @return bool
         */
        public function has_permission(string $permission): bool
        {
            if($this->Permissions !== null)
            {
                if(in_array($permission, $this->Permissions))
                {
                    return true;
                }
            }

            return false;
        }

        /**
         * Creates array from object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'access_token' => $this->AccessToken,
                'application_id' => (int)$this->ApplicationId,
                'account_id' => (int)$this->AccountId,
                'request_id' => (int)$this->RequestId,
                'permissions' => $this->Permissions,
                'status' => (int)$this->Status,
                'expires_timestamp' => (int)$this->ExpiresTimestamp,
                'last_used_timestamp' => (int)$this->LastUsedTimestamp,
                'created_timestamp' => $this->CreatedTimestamp
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return AuthenticationAccess
         */
        public static function fromArray(array $data): AuthenticationAccess
        {
            $AuthenticationAccessObject = new AuthenticationAccess();

            if(isset($data['id']))
            {
                $AuthenticationAccessObject->ID = (int)$data['id'];
            }

            if(isset($data['access_token']))
            {
                $AuthenticationAccessObject->AccessToken = $data['access_token'];
            }

            if(isset($data['application_id']))
            {
                $AuthenticationAccessObject->ApplicationId = (int)$data['application_id'];
            }

            if(isset($data['account_id']))
            {
                $AuthenticationAccessObject->AccountId = (int)$data['account_id'];
            }

            if(isset($data['request_id']))
            {
                $AuthenticationAccessObject->RequestId = (int)$data['request_id'];
            }

            if(isset($data['permissions']))
            {
                $AuthenticationAccessObject->Permissions = $data['permissions'];
            }
            else
            {
                $AuthenticationAccessObject->Permissions = [];
            }

            if(isset($data['status']))
            {
                $AuthenticationAccessObject->Status = (int)$data['status'];
            }

            if(isset($data['expires_timestamp']))
            {
                $AuthenticationAccessObject->ExpiresTimestamp = (int)$data['expires_timestamp'];
            }

            if(isset($data['last_used_timestamp']))
            {
                $AuthenticationAccessObject->LastUsedTimestamp = (int)$data['last_used_timestamp'];
            }

            if(isset($data['created_timestamp']))
            {
                $AuthenticationAccessObject->CreatedTimestamp = (int)$data['created_timestamp'];
            }

            return $AuthenticationAccessObject;
        }
    }