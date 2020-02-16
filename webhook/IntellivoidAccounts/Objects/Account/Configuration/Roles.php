<?php


    namespace IntellivoidAccounts\Objects\Account\Configuration;


    class Roles
    {
        /**
         * The active roles associated with this account
         *
         * @var array
         */
        public $Roles;

        /**
         * Roles constructor.
         */
        public function __construct()
        {
            $this->Roles = [];
        }

        /**
         * Applies a role to the account
         *
         * @param string $name
         * @return bool
         */
        public function apply_role(string $name): bool
        {
            if($this->has_role($name))
            {
                return false;
            }

            $name = strtoupper($name);
            $this->Roles[] = $name;
            return true;
        }

        /**
         * Revokes a role if it's already applied
         *
         * @param string $name
         * @return bool
         */
        public function revoke_role(string $name): bool
        {
            if($this->has_role($name) == false)
            {
                return false;
            }

            $name = strtoupper($name);
            $this->Roles = array_diff($this->Roles, [$name]);
            return true;
        }

        /**
         * Determines if the role is applied
         *
         * @param string $name
         * @return bool
         */
        public function has_role(string $name): bool
        {
            $name = strtoupper($name);

            if(in_array($name, $this->Roles))
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
                'roles' => $this->Roles
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return Roles
         */
        public static function fromArray(array $data): Roles
        {
            $RolesObject = new Roles();

            if(isset($data['roles']))
            {
                $RolesObject->Roles = $data['roles'];
            }

            return $RolesObject;
        }
    }