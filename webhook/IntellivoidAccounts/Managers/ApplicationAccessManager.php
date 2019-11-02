<?php

    /** @noinspection DuplicatedCode */
    /** @noinspection PhpUnused */

    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\ApplicationAccessStatus;
    use IntellivoidAccounts\Abstracts\SearchMethods\ApplicationAccessSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\ApplicationSearchMethod;
    use IntellivoidAccounts\Exceptions\ApplicationAccessNotFoundException;
    use IntellivoidAccounts\Exceptions\ApplicationNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\ApplicationAccess;
    use IntellivoidAccounts\Utilities\Hashing;
    use msqg\QueryBuilder;
    use ZiProto\ZiProto;

    /**
     * Class ApplicationAccessManager
     * @package IntellivoidAccounts\Managers
     */
    class ApplicationAccessManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * ApplicationAccessManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Registers the Application Access into the Database
         *
         * @param int $application_id
         * @param int $account_id
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws ApplicationNotFoundException
         */
        public function createApplicationAccess(int $application_id, int $account_id): bool
        {
            $creation_timestamp = (int)time();
            $application_id = (int)$application_id;
            $account_id = (int)$account_id;
            $public_id = Hashing::ApplicationAccess($account_id, $application_id);
            $public_id = $this->intellivoidAccounts->database->real_escape_string($public_id);
            $status = (int)ApplicationAccessStatus::Authorized;
            $last_authenticated_timestamp = $creation_timestamp;

            $application = $this->intellivoidAccounts->getApplicationManager()->getApplication(ApplicationSearchMethod::byId, $application_id);
            $permissions = ZiProto::encode($application->Permissions);
            $permissions = $this->intellivoidAccounts->database->real_escape_string($permissions);

            $Query = QueryBuilder::insert_into('application_access', array(
                'public_id' => $public_id,
                'application_id' => $application_id,
                'account_id' => $account_id,
                'permissions' => $permissions,
                'status' => $status,
                'creation_timestamp' => $creation_timestamp,
                'last_authenticated_timestamp' => $last_authenticated_timestamp
            ));
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
         * Retrieves an Application Access Object from the Database
         *
         * @param string $search_method
         * @param string $value
         * @return ApplicationAccess
         * @throws ApplicationAccessNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function getApplicationAccess(string $search_method, string $value): ApplicationAccess
        {
            switch($search_method)
            {
                case ApplicationAccessSearchMethod::byPublicId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                case ApplicationAccessSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('application_access', [
                'id',
                'public_id',
                'application_id',
                'account_id',
                'permissions',
                'status',
                'creation_timestamp',
                'last_authenticated_timestamp'
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
                    throw new ApplicationAccessNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                if($Row['permissions'] == null)
                {
                    $Row['permissions'] = [];
                }
                else
                {
                    $Row['permissions'] = ZiProto::decode($Row['permissions']);
                }

                return ApplicationAccess::fromArray($Row);
            }
        }

        /**
         * Updates an existing Application Access object in the database
         *
         * @param ApplicationAccess $applicationAccess
         * @return bool
         * @throws ApplicationAccessNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function updateApplicationAccess(ApplicationAccess $applicationAccess): bool
        {
            $this->getApplicationAccess(ApplicationAccessSearchMethod::byId, $applicationAccess->ID);

            $status = (int)$applicationAccess->Status;
            $last_authenticated_timestamp = (int)$applicationAccess->LastAuthenticatedTimestamp;
            $permissions = ZiProto::encode($applicationAccess->Permissions);
            $permissions = $this->intellivoidAccounts->database->real_escape_string($permissions);

            $Query = QueryBuilder::update('application_access', array(
                'status' => $status,
                'last_authenticated_timestamp' => $last_authenticated_timestamp,
                'permissions' => $permissions
            ), 'id', $applicationAccess->ID);
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
         * Returns the total records by Application ID
         *
         * @param int $application_id
         * @return int
         * @throws DatabaseException
         */
        public function getTotalRecordsOfApplication(int $application_id): int
        {
            $application_id = (int)$application_id;
            $Query = "SELECT COUNT(id) AS total FROM `application_access` WHERE application_id=$application_id";

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
         * Returns the total records by Account ID
         *
         * @param int $account_id
         * @return int
         * @throws DatabaseException
         */
        public function getTotalRecordsOfAccount(int $account_id): int
        {
            $account_id = (int)$account_id;
            $Query = "SELECT COUNT(id) AS total FROM `application_access` WHERE account_id=$account_id";

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
         * Returns records by Application
         *
         * @param string $application_id
         * @return array
         * @throws DatabaseException
         */
        public function searchRecordsByApplication(string $application_id): array
        {
            $Query = QueryBuilder::select("application_access", [
                'id',
                'public_id',
                'application_id',
                'account_id',
                'permissions',
                'status',
                'creation_timestamp',
                'last_authenticated_timestamp'
            ], 'application_id', $application_id);

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
                    if($Row['permissions'] == null)
                    {
                        $Row['permissions'] = [];
                    }
                    else
                    {
                        $Row['permissions'] = ZiProto::decode($Row['permissions']);
                    }
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }

        /**
         * Returns records by Account
         *
         * @param string $account_id
         * @return array
         * @throws DatabaseException
         */
        public function searchRecordsByAccount(string $account_id): array
        {
            $Query = QueryBuilder::select("application_access", [
                'id',
                'public_id',
                'application_id',
                'account_id',
                'permissions',
                'status',
                'creation_timestamp',
                'last_authenticated_timestamp'
            ], 'account_id', $account_id);

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
                    if($Row['permissions'] == null)
                    {
                        $Row['permissions'] = [];
                    }
                    else
                    {
                        $Row['permissions'] = ZiProto::decode($Row['permissions']);
                    }
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }

        /**
         * Syncs the Application Access object in the Database
         *
         * @param int $application_id
         * @param int $account_id
         * @return ApplicationAccess
         * @throws ApplicationAccessNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws ApplicationNotFoundException
         */
        public function syncApplicationAccess(int $application_id, int $account_id): ApplicationAccess
        {
            $PublicID = Hashing::ApplicationAccess($account_id, $application_id);

            try
            {
                $Application = $this->getApplicationAccess(ApplicationAccessSearchMethod::byPublicId, $PublicID);
                return $Application;
            }
            catch(ApplicationAccessNotFoundException $applicationAccessNotFoundException)
            {
                $this->createApplicationAccess($application_id, $account_id);
            }

            return $this->getApplicationAccess(ApplicationAccessSearchMethod::byPublicId, $PublicID);
        }
    }