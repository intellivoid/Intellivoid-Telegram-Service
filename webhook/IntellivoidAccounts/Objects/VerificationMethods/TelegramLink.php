<?php


    namespace IntellivoidAccounts\Objects\VerificationMethods;


    /**
     * Class TelegramLink
     * @package IntellivoidAccounts\Objects\VerificationMethods
     */
    class TelegramLink
    {
        /**
         * Indicates if this method is enabled or not
         *
         * @var bool
         */
        public $Enabled;

        /**
         * The Unix Timestamp of when the Telegram Client was last linked
         *
         * @var int
         */
        public $LastLinked;

        /**
         * Internal Unique Database ID for the Telegram Client (telegram_clients)
         *
         * @var string
         */
        public $ClientId;

        /**
         * TelegramLink constructor.
         */
        public function __construct()
        {
            $this->Enabled = false;
            $this->LastLinked = 0;
            $this->ClientId = 0;
        }

        /**
         * Enables this method of Verification
         *
         * @param int $telegram_client_id
         */
        public function enable(int $telegram_client_id)
        {
            $this->Enabled = true;
            $this->LastLinked = (int)time();
            $this->ClientId = $telegram_client_id;
        }

        /**
         * Disables this method of authentication
         */
        public function disable()
        {
            $this->Enabled = false;
            $this->LastLinked = 0;
            $this->ClientId = 0;
        }

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'enabled' => (bool)$this->Enabled,
                'last_linked' => (int)$this->LastLinked,
                'client_id' => (int)$this->ClientId
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return TelegramLink
         */
        public static function fromArray(array $data): TelegramLink
        {
            $TelegramLinkObject = new TelegramLink();

            if(isset($data['enabled']))
            {
                $TelegramLinkObject->Enabled = (bool)$data['enabled'];
            }

            if(isset($data['last_linked']))
            {
                $TelegramLinkObject->LastLinked = (int)$data['last_linked'];
            }

            if(isset($data['client_id']))
            {
                $TelegramLinkObject->ClientId = (int)$data['client_id'];
            }

            return $TelegramLinkObject;
        }
    }