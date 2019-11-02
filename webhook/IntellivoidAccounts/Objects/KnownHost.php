<?php


    namespace IntellivoidAccounts\Objects;


    class KnownHost
    {
        /**
         * The internal database ID for this host
         *
         * @var int
         */
        public $ID;

        /**
         * The Public ID for this host record
         *
         * @var string
         */
        public $PublicID;

        /**
         * The IP Address
         *
         * @var string
         */
        public $IpAddress;

        /**
         * Indicates if this host was blocked by the user
         *
         * @var bool
         */
        public $Blocked;

        /**
         * Unix Timestamp for when this host was last used
         *
         * @var int
         */
        public $LastUsed;

        /**
         * The location data associated with this host
         *
         * @var LocationData
         */
        public $LocationData;

        /**
         * The Unix Timestamp for when this host was registered into the system
         *
         * @var int
         */
        public $Created;

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'public_id' => $this->PublicID,
                'ip_address' => $this->IpAddress,
                'blocked' => (bool)$this->Blocked,
                'last_used' => (int)$this->LastUsed,
                'location_data' => $this->LocationData->toArray(),
                'created' => $this->LastUsed
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return KnownHost
         */
        public static function fromArray(array $data): KnownHost
        {
            $KnownHostObject = new KnownHost();

            if(isset($data['id']))
            {
                $KnownHostObject->ID = (int)$data['id'];
            }

            if(isset($data['public_id']))
            {
                $KnownHostObject->PublicID = $data['public_id'];
            }

            if(isset($data['ip_address']))
            {
                $KnownHostObject->IpAddress = $data['ip_address'];
            }

            if(isset($data['blocked']))
            {
                $KnownHostObject->Blocked = (bool)$data['blocked'];
            }

            if(isset($data['last_used']))
            {
                $KnownHostObject->LastUsed = (int)$data['last_used'];
            }

            if(isset($data['location_data']))
            {
                $KnownHostObject->LocationData = LocationData::fromArray($data['location_data']);
            }
            else
            {
                $KnownHostObject->LocationData = new LocationData();
            }

            if(isset($data['created']))
            {
                $KnownHostObject->Created = (int)$data['created'];
            }

            return $KnownHostObject;
        }
    }