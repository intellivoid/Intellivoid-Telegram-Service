<?php /** @noinspection PhpUnused */

    namespace IntellivoidAccounts\Managers;

    use IntellivoidAccounts\Abstracts\LoginStatus;
    use IntellivoidAccounts\Abstracts\SearchMethods\KnownHostsSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\LoginRecordMultiSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\LoginRecordSearchMethod;
    use IntellivoidAccounts\Exceptions\AccountNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\HostNotKnownException;
    use IntellivoidAccounts\Exceptions\InvalidIpException;
    use IntellivoidAccounts\Exceptions\InvalidLoginStatusException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\LoginRecordNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\UserAgent;
    use IntellivoidAccounts\Objects\UserLoginRecord;
    use IntellivoidAccounts\Utilities\Hashing;
    use IntellivoidAccounts\Utilities\Validate;
    use msqg\Abstracts\SortBy;
    use msqg\QueryBuilder;
    use ZiProto\ZiProto;

    /**
     * Class LoginRecordManager
     * @package IntellivoidAccounts\Managers
     */
    class LoginRecordManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * LoginRecordManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }


        /**
         * @param int $account_id
         * @param int $known_host_id
         * @param LoginStatus|int $status
         * @param string $origin
         * @param string $user_agent
         * @return bool
         * @throws AccountNotFoundException
         * @throws DatabaseException
         * @throws HostNotKnownException
         * @throws InvalidIpException
         * @throws InvalidLoginStatusException
         * @throws InvalidSearchMethodException
         */
        public function createLoginRecord(int $account_id, int $known_host_id, int $status, string $origin, string $user_agent): bool
        {
            if($this->intellivoidAccounts->getAccountManager()->IdExists($account_id) == false)
            {
                throw new AccountNotFoundException();
            }

            $this->intellivoidAccounts->getKnownHostsManager()->getHost(KnownHostsSearchMethod::byId, $known_host_id);

            // NOTE: Removed "SyncHost" call here because it is no longer attached to an account ID, the account
            // configuration is attached to the HostID instead.

            switch($status)
            {
                case LoginStatus::BlockedSuspiciousActivities:
                case LoginStatus::IncorrectCredentials:
                case LoginStatus::VerificationFailed:
                case LoginStatus::UntrustedIpBlocked:
                case LoginStatus::Successful:
                case LoginStatus::Unknown:
                    break;

                default:
                    throw new InvalidLoginStatusException();
            }

            $account_id = (int)$account_id;
            $known_host_id = (int)$known_host_id;
            $login_status = (int)$status;
            $origin = $this->intellivoidAccounts->database->real_escape_string($origin);
            $timestamp = (int)time();
            $public_id = Hashing::loginPublicID($account_id, $timestamp, $login_status, $origin);
            $public_id = $this->intellivoidAccounts->database->real_escape_string($public_id);
            $user_agent_x = null;
            if(Validate::userAgent($user_agent))
            {
                $user_agent_x = UserAgent::fromString($user_agent);
            }
            else
            {
                $user_agent_x = new UserAgent();
                $user_agent_x->UserAgentString = "None";
            }
            $user_agent_x = $this->intellivoidAccounts->database->real_escape_string(ZiProto::encode($user_agent_x->toArray()));

            $Query = "INSERT INTO `users_logins` (public_id, origin, host_id, user_agent, account_id, status, timestamp) VALUES ('$public_id', '$origin', $known_host_id, '$user_agent_x', $account_id, $status, $timestamp)";
            $QueryResults = $this->intellivoidAccounts->database->query($Query);

            if($QueryResults == true)
            {
                return true;
            }
            else
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
        }

        /**
         * Gets an existing Login Record from the database
         *
         * @param string $search_method
         * @param string $value
         * @return UserLoginRecord
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws LoginRecordNotFoundException
         */
        public function getLoginRecord(string $search_method, string $value): UserLoginRecord
        {
            switch($search_method)
            {
                case LoginRecordSearchMethod::byPublicId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                case LoginRecordSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select("users_logins", [
                'id',
                'public_id',
                'origin',
                'host_id',
                'user_agent',
                'account_id',
                'status',
                'timestamp'
            ], $search_method, $value);

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new LoginRecordNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['user_agent'] = ZiProto::decode($Row['user_agent']);
                return UserLoginRecord::fromArray($Row);
            }
        }


        /**
         * Searches for login records and returns an array of login records
         *
         * @param string $search_method
         * @param string $value
         * @param int $limit
         * @param int $offset
         * @return array
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function searchRecords(string $search_method, string $value, int $limit=100, int $offset=0): array
        {
            switch($search_method)
            {
                case LoginRecordMultiSearchMethod::byIpAddress:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                case LoginRecordMultiSearchMethod::byAccountId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select("users_logins", [
                'id',
                'public_id',
                'origin',
                'host_id',
                'user_agent',
                'account_id',
                'status',
                'timestamp'
            ], $search_method, $value, 'timestamp', SortBy::descending, $limit, $offset);

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
                    $Row['user_agent'] = ZiProto::decode($Row['user_agent']);
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }

        /**
         * Counts the total amount of records that are found
         *
         * @param string $search_method
         * @param string $value
         * @return int
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function getTotalRecords(string $search_method, string $value): int
        {
            switch($search_method)
            {
                case LoginRecordMultiSearchMethod::byIpAddress:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = "'" . $this->intellivoidAccounts->database->real_escape_string($value) . "'";
                    break;

                case LoginRecordMultiSearchMethod::byAccountId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = "SELECT COUNT(id) AS total FROM `users_logins` WHERE $search_method=$value";

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
    }