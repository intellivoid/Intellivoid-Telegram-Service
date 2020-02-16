<?php


    namespace IntellivoidAccounts\Objects;


    /**
     * Class TransactionRecord
     * @package IntellivoidAccounts\Objects
     */
    class TransactionRecord
    {
        /**
         * Unique internal database ID
         *
         * @var int
         */
        public $ID;

        /**
         * Unique Public ID of this transaction
         *
         * @var string
         */
        public $PublicID;

        /**
         * The account ID that this transaction is associated with
         *
         * @var int
         */
        public $AccountID;

        /**
         * The name of the vendor or account username that this transaction is for/from
         *
         * @var string
         */
        public $Vendor;

        /**
         * The amount that was given/taken from the account
         *
         * @var float
         */
        public $Amount;

        /**
         * The Unix Timestamp of when this transaction took place
         *
         * @var int
         */
        public $Timestamp;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'public_id' => $this->PublicID,
                'account_id' => (int)$this->AccountID,
                'vendor' => $this->Vendor,
                'amount' => (float)$this->Amount,
                'timestamp' => (int)$this->Timestamp
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return TransactionRecord
         */
        public static function fromArray(array $data): TransactionRecord
        {
            $TransactionRecordObject = new TransactionRecord();

            if(isset($data['id']))
            {
                $TransactionRecordObject->ID = (int)$data['id'];
            }

            if(isset($data['public_id']))
            {
                $TransactionRecordObject->PublicID = $data['public_id'];
            }

            if(isset($data['account_id']))
            {
                $TransactionRecordObject->AccountID = (int)$data['account_id'];
            }

            if(isset($data['vendor']))
            {
                $TransactionRecordObject->Vendor = $data['vendor'];
            }

            if(isset($data['amount']))
            {
                $TransactionRecordObject->Amount = (float)$data['amount'];
            }

            if(isset($data['timestamp']))
            {
                $TransactionRecordObject->Timestamp = (int)$data['timestamp'];
            }

            return $TransactionRecordObject;
        }
    }