<?php


    namespace IntellivoidAccounts\Objects;

    /**
     * Class AuditRecord
     * @package IntellivoidAccounts\Objects
     */
    class AuditRecord
    {
        /**
         * Internal unique Database ID for this Audit Record
         *
         * @var int
         */
        public $ID;

        /**
         * The account ID associated with this record
         *
         * @var int
         */
        public $AccountID;

        /**
         * The event type
         *
         * @var int
         */
        public $EventType;

        /**
         * The Unix Timestamp of when this record was created
         *
         * @var int
         */
        public $Timestamp;

        /**
         * AuditRecord constructor.
         */
        public function __construct()
        {
            $this->ID = 0;
            $this->AccountID = 0;
            $this->EventType = 0;
            $this->Timestamp = 0;
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => $this->ID,
                'account_id' => $this->AccountID,
                'event_type' => $this->EventType,
                'timestamp' => $this->Timestamp
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return AuditRecord
         */
        public static function fromArray(array $data): AuditRecord
        {
            $AuditRecordObject = new AuditRecord();

            if(isset($data['id']))
            {
                $AuditRecordObject->ID = $data['id'];
            }

            if(isset($data['account_id']))
            {
                $AuditRecordObject->AccountID = $data['account_id'];
            }

            if(isset($data['event_type']))
            {
                $AuditRecordObject->EventType = $data['event_type'];
            }

            if(isset($data['timestamp']))
            {
                $AuditRecordObject->Timestamp = $data['timestamp'];
            }

            return $AuditRecordObject;
        }
    }