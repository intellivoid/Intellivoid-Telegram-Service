<?php


    namespace IntellivoidAccounts\Objects;

    /**
     * Class OneTimeLoginCode
     * @package IntellivoidAccounts\Objects
     */
    class OneTimeLoginCode
    {
        /**
         * Unique internal database ID representing this record
         *
         * @var int
         */
        public $ID;

        /**
         * The unique login verification code
         *
         * @var string
         */
        public $Code;

        /**
         * The Application/Service that authenticated using this code (Default; None)
         *
         * @default None
         * @var string
         */
        public $Vendor;

        /**
         * The Account ID that requested this login code
         *
         * @var int
         */
        public $AccountID;

        /**
         * The status of the verification code
         *
         * @var int
         */
        public $Status;

        /**
         * The Unix Timestamp of when this login code expires
         *
         * @var int
         */
        public $ExpiresTimestamp;

        /**
         * The Unix Timestamp of when this login code was created
         *
         * @var int
         */
        public $CreatedTimestamp;

        /**
         * Returns an array that represents this object's structure
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'code' => $this->Code,
                'vendor' => $this->Vendor,
                'account_id' => (int)$this->AccountID,
                'status' => (int)$this->Status,
                'expires' => (int)$this->ExpiresTimestamp,
                'created' => (int)$this->CreatedTimestamp
            );
        }

        /**
         * Constructs an object from an array
         *
         * @param array $data
         * @return OneTimeLoginCode
         */
        public static function fromArray(array $data): OneTimeLoginCode
        {
            $OneTimeLoginCodeObject = new OneTimeLoginCode();

            if(isset($data['id']))
            {
                $OneTimeLoginCodeObject->ID = (int)$data['id'];
            }

            if(isset($data['code']))
            {
                $OneTimeLoginCodeObject->Code = $data['code'];
            }

            if(isset($data['vendor']))
            {
                $OneTimeLoginCodeObject->Vendor = $data['vendor'];
            }

            if(isset($data['account_id']))
            {
                $OneTimeLoginCodeObject->AccountID = (int)$data['account_id'];
            }

            if(isset($data['status']))
            {
                $OneTimeLoginCodeObject->Status = (int)$data['status'];
            }

            if(isset($data['expires']))
            {
                $OneTimeLoginCodeObject->ExpiresTimestamp = (int)$data['expires'];
            }

            if(isset($data['created']))
            {
                $OneTimeLoginCodeObject->CreatedTimestamp = (int)$data['created'];
            }

            return $OneTimeLoginCodeObject;
        }
    }