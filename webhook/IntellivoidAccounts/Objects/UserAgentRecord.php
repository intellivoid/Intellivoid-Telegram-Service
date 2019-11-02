<?php


    namespace IntellivoidAccounts\Objects;


    /**
     * Class UserAgentRecord
     * @package IntellivoidAccounts\Objects
     */
    class UserAgentRecord
    {
        /**
         * The unique internal database ID for this user agent record
         *
         * @var int
         */
        public $ID;

        /**
         * The calculated tracking ID
         *
         * @var string
         */
        public $TrackingID;

        /**
         * The full UserAgent string
         *
         * @var string
         */
        public $UserAgentString;

        /**
         * The detected platform form the UserAgent
         *
         * @var string
         */
        public $Platform;

        /**
         * The detected platform from the UserAgent
         *
         * @var string
         */
        public $Browser;

        /**
         * The detected version from the UserAgent
         *
         * @var string
         */
        public $Version;

        /**
         * The host ID associated with this record
         *
         * @var int
         */
        public $HostID;

        /**
         * The Unix Timestamp of when this record was created
         *
         * @var int
         */
        public $Created;

        /**
         * The Unix Timestamp of when this useragent was last used by the host
         *
         * @var int
         */
        public $LastSeen;

        /**
         * Returns an array that represents the structure of this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'tracking_id' => $this->TrackingID,
                'user_agent_string' => $this->UserAgentString,
                'platform' => $this->Platform,
                'browser' => $this->Browser,
                'version' => $this->Version,
                'host_id' => (int)$this->HostID,
                'created' => (int)$this->Created,
                'last_seen' => (int)$this->LastSeen
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return UserAgentRecord
         */
        public static function fromArray(array $data): UserAgentRecord
        {
            $UserAgentRecordObject = new UserAgentRecord();

            if(isset($data['id']))
            {
                $UserAgentRecordObject->ID = (int)$data['id'];
            }

            if(isset($data['tracking_id']))
            {
                $UserAgentRecordObject->TrackingID = $data['tracking_id'];
            }

            if(isset($data['user_agent_string']))
            {
                $UserAgentRecordObject->UserAgentString = $data['user_agent_string'];
            }

            if(isset($data['platform']))
            {
                $UserAgentRecordObject->Platform = $data['platform'];
            }

            if(isset($data['browser']))
            {
                $UserAgentRecordObject->Browser = $data['browser'];
            }

            if(isset($data['version']))
            {
                $UserAgentRecordObject->Version = $data['version'];
            }

            if(isset($data['host_id']))
            {
                $UserAgentRecordObject->HostID = (int)$data['host_id'];
            }

            if(isset($data['created']))
            {
                $UserAgentRecordObject->Created = (int)$data['created'];
            }

            if(isset($data['last_seen']))
            {
                $UserAgentRecordObject->LastSeen = (int)$data['last_seen'];
            }

            return $UserAgentRecordObject;
        }
    }