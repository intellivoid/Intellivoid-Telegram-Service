<?php /** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Managers;

    use IntellivoidAccounts\Abstracts\AuditEventType;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidEventTypeException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use msqg\Abstracts\SortBy;
    use msqg\QueryBuilder;

    /**
     * Class AuditLogManager
     * @package IntellivoidAccounts\Managers
     */
    class AuditLogManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * AuditLogManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Records an audit event for an account
         *
         * @param int $account_id
         * @param int $event_type
         * @return bool
         * @throws DatabaseException
         * @throws InvalidEventTypeException
         */
        public function logEvent(int $account_id, int $event_type): bool
        {
            switch($event_type)
            {
                case AuditEventType::NewLoginDetected:
                case AuditEventType::PasswordUpdated:
                case AuditEventType::PersonalInformationUpdated:
                case AuditEventType::EmailUpdated:
                case AuditEventType::MobileVerificationEnabled:
                case AuditEventType::MobileVerificationDisabled:
                case AuditEventType::RecoveryCodesEnabled:
                case AuditEventType::RecoveryCodesDisabled:
                case AuditEventType::TelegramVerificationEnabled:
                case AuditEventType::TelegramVerificationDisabled:
                case AuditEventType::ApplicationCreated:
                case AuditEventType::NewLoginLocationDetected:
                    break;

                default:
                    throw new InvalidEventTypeException();
            }

            $account_id = (int)$account_id;
            $event_type = (int)$event_type;
            $timestamp = (int)time();

            $Query = QueryBuilder::insert_into('users_audit', array(
                'account_id' => $account_id,
                'event_type' => $event_type,
                'timestamp' => $timestamp
            ));

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                return True;
            }
        }

        /**
         * Counts the total amount of records that are found
         *
         * @param int $account_id
         * @return int
         * @throws DatabaseException
         */
        public function getTotalRecords(int $account_id): int
        {
            $account_id = (int)$account_id;
            $Query = "SELECT COUNT(id) AS total FROM `users_audit` WHERE account_id=$account_id";

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                return (int)$QueryResults->fetch_array()['total'];
            }
        }

        /**
         * Returns an array of User Audit Logs
         *
         * @param int $account_id
         * @param int $offset
         * @param int $limit
         * @return array
         * @throws DatabaseException
         */
        public function getRecords(int $account_id, int $offset = 0, $limit = 50): array
        {
            $account_id = (int)$account_id;

            $Query = QueryBuilder::select('users_audit', [
                'id',
                'account_id',
                'event_type',
                'timestamp'
            ], 'account_id', $account_id, null, null, $limit, $offset);

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }

        /**
         * Returns the newer recent audit records
         *
         * @param int $account_id
         * @param int $limit
         * @return array
         * @throws DatabaseException
         */
        public function getNewRecords(int $account_id, $limit = 50): array
        {
            $account_id = (int)$account_id;

            $Query = QueryBuilder::select('users_audit', [
                'id',
                'account_id',
                'event_type',
                'timestamp'
            ], 'account_id', $account_id, 'timestamp', SortBy::descending, $limit);

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }
    }