<?php


    namespace IntellivoidAccounts\Objects;

    use IntellivoidAccounts\Abstracts\LoginStatus;

    /**
     * Class UserLoginRecord
     * @package IntellivoidAccounts\Objects
     */
    class UserLoginRecord
    {
        /**
         * Internal unique database ID for this login record
         *
         * @var int
         */
        public $ID;

        /**
         * Public unique ID for this login record
         *
         * @var string
         */
        public $PublicID;

        /**
         * The origin of where this login came from
         *
         * @var string
         */
        public $Origin;

        /**
         * The host ID associated with this login record
         *
         * @var int
         */
        public $HostID;

        /**
         * Parsed user agent data associated with this login record
         *
         * @var UserAgent
         */
        public $UserAgent;

        /**
         * The account ID associated with this Login Record
         *
         * @var int
         */
        public $AccountID;

        /**
         * The status of the login
         *
         * @var LoginStatus|int
         */
        public $Status;

        /**
         * The Unix Timestamp of when this record was created
         *
         * @var int
         */
        public $Timestamp;

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => $this->ID,
                'public_id' => $this->PublicID,
                'origin' => $this->Origin,
                'host_id' => $this->HostID,
                'user_agent' => $this->UserAgent->toArray(),
                'account_id' => $this->AccountID,
                'status' => $this->Status,
                'timestamp' => $this->Timestamp
            );
        }

        /**
         * Creates an object from an array structure
         *
         * @param array $data
         * @return UserLoginRecord
         */
        public static function fromArray(array $data): UserLoginRecord
        {
            $UserLoginRecordObject = new UserLoginRecord();

            if(isset($data['id']))
            {
                $UserLoginRecordObject->ID = (int)$data['id'];
            }

            if(isset($data['public_id']))
            {
                $UserLoginRecordObject->PublicID = $data['public_id'];
            }

            if(isset($data['origin']))
            {
                $UserLoginRecordObject->Origin = $data['origin'];
            }

             if(isset($data['host_id']))
             {
                 $UserLoginRecordObject->HostID = (int)$data['host_id'];
             }

             if(isset($data['user_agent']))
             {
                 $UserLoginRecordObject->UserAgent = UserAgent::fromArray($data['user_agent']);
             }

             if(isset($data['account_id']))
             {
                 $UserLoginRecordObject->AccountID = (int)$data['account_id'];
             }

             if(isset($data['status']))
             {
                 $UserLoginRecordObject->Status = (int)$data['status'];
             }

             if(isset($data['timestamp']))
             {
                 $UserLoginRecordObject->Timestamp = (int)$data['timestamp'];
             }

            return $UserLoginRecordObject;
        }
    }