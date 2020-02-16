<?php /** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\SearchMethods\TrackingUserAgentSearchMethod;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\UserAgentNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\UserAgent;
    use IntellivoidAccounts\Objects\UserAgentRecord;
    use IntellivoidAccounts\Utilities\Hashing;
    use IntellivoidAccounts\Utilities\Validate;
    use msqg\QueryBuilder;

    /**
     * Class TrackingUserAgentManager
     * @package IntellivoidAccounts\Managers
     */
    class TrackingUserAgentManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * TrackingUserAgentManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Registers a new record into the database, returns the tracking id
         *
         * @param string $user_agent_string
         * @param int $host_id
         * @return string
         * @throws DatabaseException
         */
        public function registerRecord(string $user_agent_string, int $host_id): string
        {
            if(Validate::userAgent($user_agent_string))
            {
                $user_agent_parse = UserAgent::fromString($user_agent_string);
            }
            else
            {
                $user_agent_parse = new UserAgent();
                $user_agent_parse->Browser = "Unknown";
                $user_agent_parse->Platform = "Unknown";
                $user_agent_parse->Version = "Unknown";
                $user_agent_string = "Unknown";
            }

            $user_agent_string = $this->intellivoidAccounts->database->real_escape_string(base64_encode($user_agent_string));
            $tracking_id = Hashing::uaTrackingId($user_agent_string, $host_id);
            $tracking_id = $this->intellivoidAccounts->database->real_escape_string($tracking_id);
            $created = (int)time();

            $host_id = (int)$host_id;
            $platform = 'Unknown';
            $browser = 'Unknown';
            $version = 'Unknown';

            try
            {
                $user_agent_record = $this->getRecord(TrackingUserAgentSearchMethod::byTrackingId, $tracking_id);
                $user_agent_record->LastSeen = (int)time();
                $this->updateRecord($user_agent_record);
                return $tracking_id;
            }
            catch(UserAgentNotFoundException $userAgentNotFoundException)
            {
                // Ignore this exception
                unset($userAgentNotFoundException);
            }

            if($user_agent_parse->Platform !== null)
            {
                $platform = $this->intellivoidAccounts->database->real_escape_string($user_agent_parse->Platform);
            }

            if($user_agent_parse->Browser !== null)
            {
                $browser = $this->intellivoidAccounts->database->real_escape_string($user_agent_parse->Browser);
            }

            if($user_agent_parse->Version !== null)
            {
                $version = $this->intellivoidAccounts->database->real_escape_string($user_agent_parse->Version);
            }

            $Query = QueryBuilder::insert_into('tracking_user_agents', array(
                'tracking_id' => $tracking_id,
                'user_agent_string' => $user_agent_string,
                'platform' => $platform,
                'browser' => $browser,
                'version' => $version,
                'host_id' => $host_id,
                'created' => $created,
                'last_seen' => $created
            ));
            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                return $tracking_id;
            }
        }

        /**
         * Gets an existing record from the database
         *
         * @param string $search_method
         * @param string $value
         * @return UserAgentRecord
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws UserAgentNotFoundException
         */
        public function getRecord(string $search_method, string $value): UserAgentRecord
        {
            switch($search_method)
            {
                case TrackingUserAgentSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value =  (int)$value;
                    break;

                case TrackingUserAgentSearchMethod::byTrackingId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('tracking_user_agents', [
                'id',
                'tracking_id',
                'user_agent_string',
                'platform',
                'browser',
                'version',
                'host_id',
                'created',
                'last_seen'
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
                    throw new UserAgentNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['user_agent_string'] = base64_decode($Row['user_agent_string']);

                return UserAgentRecord::fromArray($Row);
            }
        }

        /**
         * Updates an existing record in the database
         *
         * @param UserAgentRecord $userAgentRecord
         * @param bool $check
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws UserAgentNotFoundException
         */
        public function updateRecord(UserAgentRecord $userAgentRecord, bool $check = false): bool
        {
            if($check)
            {
                $this->getRecord(TrackingUserAgentSearchMethod::byId, $userAgentRecord->ID);
            }

            if(Validate::userAgent($userAgentRecord->UserAgentString))
            {
                $user_agent_parse = UserAgent::fromString($userAgentRecord->UserAgentString);
            }
            else
            {
                $user_agent_parse = new UserAgent();
                $user_agent_parse->Browser = "Unknown";
                $user_agent_parse->Platform = "Unknown";
                $user_agent_parse->Version = "Unknown";
                $userAgentRecord->UserAgentString = "Unknown";
            }

            $tracking_id = Hashing::uaTrackingId($userAgentRecord->UserAgentString, $userAgentRecord->HostID);
            $tracking_id = $this->intellivoidAccounts->database->real_escape_string($tracking_id);
            $user_agent_string = $this->intellivoidAccounts->database->real_escape_string(base64_encode($userAgentRecord->UserAgentString));
            $platform = 'Unknown';
            $browser = 'Unknown';
            $version = 'Unknown';
            $last_seen = (int)$userAgentRecord->LastSeen;

            /** @noinspection DuplicatedCode */
            if($user_agent_parse->Platform !== null)
            {
                $platform = $this->intellivoidAccounts->database->real_escape_string($user_agent_parse->Platform);
            }

            /** @noinspection DuplicatedCode */
            if($user_agent_parse->Browser !== null)
            {
                $browser = $this->intellivoidAccounts->database->real_escape_string($user_agent_parse->Browser);
            }

            /** @noinspection DuplicatedCode */
            if($user_agent_parse->Version !== null)
            {
                $version = $this->intellivoidAccounts->database->real_escape_string($user_agent_parse->Version);
            }

            $Query = QueryBuilder::update('tracking_user_agents', array(
                'tracking_id' => $tracking_id,
                'user_agent_string' => $user_agent_string,
                'platform' => $platform,
                'browser' => $browser,
                'version' => $version,
                'last_seen' => $last_seen
            ), 'id', $userAgentRecord->ID);
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
         * Syncs a record intelligently into the database, returns tracking ID
         *
         * @param string $user_agent_string
         * @param int $host_id
         * @return string
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws UserAgentNotFoundException
         */
        public function syncRecord(string $user_agent_string, int $host_id): string
        {
            $tracking_id = Hashing::uaTrackingId($user_agent_string, $host_id);

            try
            {
                $user_agent_record = $this->getRecord(TrackingUserAgentSearchMethod::byTrackingId, $tracking_id);
            }
            catch(UserAgentNotFoundException $userAgentNotFoundException)
            {
                $this->registerRecord($user_agent_string, $host_id);
                return $tracking_id;
            }

            $user_agent_record->LastSeen = (int)time();
            $this->updateRecord($user_agent_record);
            return $tracking_id;
        }

        /**
         * Returns the total amount of Tracking User Agent records by the Host ID
         *
         * @param int $host_id
         * @return int
         * @throws DatabaseException
         */
        public function getTotalRecordsByHost(int $host_id): int
        {
            $host_id = (int)$host_id;
            $Query = "SELECT COUNT(id) AS total FROM `tracking_user_agents` WHERE host_id=$host_id";

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
         * Returns an array of Records by the Host ID
         *
         * @param int $host_id
         * @param int $offset
         * @param int $limit
         * @return array
         * @throws DatabaseException
         */
        public function getRecordsByHost(int $host_id, int $offset = 0, $limit = 50): array
        {
            $host_id = (int)$host_id;

            $Query = QueryBuilder::select('tracking_user_agents', [
                'id',
                'tracking_id',
                'user_agent_string',
                'platform',
                'browser',
                'version',
                'host_id',
                'created',
                'last_seen'
            ], 'host_id', $host_id, null, null, $limit, $offset);

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if ($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                $ResultsArray = [];

                while ($Row = $QueryResults->fetch_assoc())
                {
                    $Row['user_agent_string'] = base64_decode($Row['user_agent_string']);
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }

        }
    }