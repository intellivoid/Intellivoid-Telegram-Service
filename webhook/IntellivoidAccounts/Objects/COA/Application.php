<?php


    namespace IntellivoidAccounts\Objects\COA;

    use IntellivoidAccounts\Abstracts\AccountRequestPermissions;
    use IntellivoidAccounts\Exceptions\InvalidApplicationFlagException;
    use IntellivoidAccounts\Exceptions\InvalidRequestPermissionException;
    use IntellivoidAccounts\Utilities\Validate;

    /**
     * Class Application
     * @package IntellivoidAccounts\Objects\COA
     */
    class Application
    {
        /**
         * Unique Internal Database ID
         *
         * @var int
         */
        public $ID;

        /**
         * Public Application ID
         *
         * @var string
         */
        public $PublicAppId;

        /**
         * Secret Key for issuing access requests
         *
         * @var string
         */
        public $SecretKey;

        /**
         * The name of the application
         *
         * @var string
         */
        public $Name;

        /**
         * Safe name of the application
         *
         * @var string
         */
        public $NameSafe;

        /**
         * Permissions required by the Application
         *
         * @var array
         */
        public $Permissions;

        /**
         * The current status of the application
         *
         * @var int
         */
        public $Status;

        /**
         * The authentication mode that this application uses
         *
         * @var int
         */
        public $AuthenticationMode;

        /**
         * Account ID that owns this application
         *
         * @var int
         */
        public $AccountID;

        /**
         * Flags associated with this Application
         *
         * @var array
         */
        public $Flags;

        /**
         * The Unix Timestamp of when this Application was registered
         *
         * @var int
         */
        public $CreationTimestamp;

        /**
         * The Unix Timestamp of when this application was last updated
         *
         * @var int
         */
        public $LastUpdatedTimestamp;

        /**
         * Application constructor.
         */
        public function __construct()
        {
            $this->Permissions = [];
        }

        /**
         * Applies a permission to the application
         *
         * @param string|AccountRequestPermissions $permission
         * @return bool
         * @throws InvalidRequestPermissionException
         */
        public function apply_permission(string $permission): bool
        {
            if(isset($this->Permissions[$permission]))
            {
                return false;
            }

            if(Validate::verify_permission($permission) == false)
            {
                throw new InvalidRequestPermissionException();
            }

            $this->Permissions[] = $permission;
            return true;
        }

        /**
         * Revokes an existing permission
         *
         * @param string $permission
         * @return bool
         */
        public function revoke_permission(string $permission): bool
        {
            if(isset($this->Permissions[$permission]) == false)
            {
                return false;
            }

            unset($this->Permissions[$permission]);
            return true;
        }

        /**
         * Applies a flag to this Application
         *
         * @param string $flag
         * @return bool
         */
        public function apply_flag(string $flag): bool
        {
            if($this->has_flag($flag))
            {
                return false;
            }

            $this->Flags[] = $flag;
            return true;
        }

        /**
         * Removes an existing flag from this Application
         *
         * @param string $flag
         * @return bool
         */
        public function remove_flag(string $flag): bool
        {
            if($this->has_flag($flag) == false)
            {
                return false;
            }

            $this->Flags = array_diff($this->Flags, [$flag]);
            return true;
        }

        /**
         * Determines if the Application has the specified permission
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
         * Determines if the Application has the specified flag
         *
         * @param string $flag
         * @return bool
         */
        public function has_flag(string $flag): bool
        {
            if(in_array($flag, $this->Flags))
            {
                return true;
            }

            return false;
        }

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => $this->ID,
                'public_app_id' => $this->PublicAppId,
                'secret_key' => $this->SecretKey,
                'name' => $this->Name,
                'name_safe' => str_ireplace(' ', '_', strtolower($this->Name)),
                'permissions' => $this->Permissions,
                'status' => $this->Status,
                'authentication_mode' => $this->AuthenticationMode,
                'account_id' => $this->AccountID,
                'flags' => $this->Flags,
                'creation_timestamp' => (int)$this->CreationTimestamp,
                'last_updated_timestamp' => (int)$this->LastUpdatedTimestamp
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Application
         */
        public static function fromArray(array $data): Application
        {
            $ApplicationObject = new Application();

            if(isset($data['id']))
            {
                $ApplicationObject->ID = (int)$data['id'];
            }

            if(isset($data['public_app_id']))
            {
                $ApplicationObject->PublicAppId = $data['public_app_id'];
            }

            if(isset($data['secret_key']))
            {
                $ApplicationObject->SecretKey = $data['secret_key'];
            }

            if(isset($data['name']))
            {
                $ApplicationObject->Name = $data['name'];
                $ApplicationObject->NameSafe = str_ireplace(' ', '_', strtolower($data['name']));
            }

            if(isset($data['permissions']))
            {
                $ApplicationObject->Permissions = $data['permissions'];
            }

            if(isset($data['status']))
            {
                $ApplicationObject->Status = (int)$data['status'];
            }

            if(isset($data['authentication_mode']))
            {
                $ApplicationObject->AuthenticationMode = (int)$data['authentication_mode'];
            }

            if(isset($data['account_id']))
            {
                $ApplicationObject->AccountID = (int)$data['account_id'];
            }

            if(isset($data['flags']))
            {
                $ApplicationObject->Flags = $data['flags'];
            }
            else
            {
                $ApplicationObject->Flags = [];
            }

            if(isset($data['creation_timestamp']))
            {
                $ApplicationObject->CreationTimestamp = (int)$data['creation_timestamp'];
            }
            else
            {
                $ApplicationObject->CreationTimestamp = 0;
            }

            if(isset($data['last_updated_timestamp']))
            {
                $ApplicationObject->LastUpdatedTimestamp = (int)$data['last_updated_timestamp'];
            }
            else
            {
                $ApplicationObject->LastUpdatedTimestamp = 0;
            }

            return $ApplicationObject;
        }
    }