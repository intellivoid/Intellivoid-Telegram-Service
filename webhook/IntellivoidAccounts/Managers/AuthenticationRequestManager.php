<?php /** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\AuthenticationRequestStatus;
    use IntellivoidAccounts\Abstracts\SearchMethods\AuthenticationRequestSearchMethod;
    use IntellivoidAccounts\Exceptions\AuthenticationRequestNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\COA\Application;
    use IntellivoidAccounts\Objects\COA\AuthenticationRequest;
    use IntellivoidAccounts\Utilities\Hashing;
    use msqg\QueryBuilder;
    use ZiProto\ZiProto;

    /**
     * Class AuthenticationRequestManager
     * @package IntellivoidAccounts\Managers
     */
    class AuthenticationRequestManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * AuthenticationRequestManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Creates a new Authentication Request
         *
         * @param Application $application
         * @param int $host_id
         * @return AuthenticationRequest
         * @throws AuthenticationRequestNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function createAuthenticationRequest(Application $application, int $host_id): AuthenticationRequest
        {
            $current_timestamp = (int)time();
            $request_token = Hashing::authenticationRequestToken(
                $application->ID, $application->Name, $host_id, $current_timestamp
            );
            $request_token = $this->intellivoidAccounts->database->real_escape_string($request_token);
            $application_id = (int)$application->ID;
            $status = (int)AuthenticationRequestStatus::Active;
            $account_id = 0;
            $host_id = (int)$host_id;
            $requested_permissions = ZiProto::encode($application->Permissions);
            $requested_permissions = $this->intellivoidAccounts->database->real_escape_string($requested_permissions);
            $created_timestamp = $current_timestamp;
            $expires_timestamp = $current_timestamp + 600;

            $Query = QueryBuilder::insert_into('authentication_requests', array(
                'request_token' => $request_token,
                'application_id' => $application_id,
                'status' => $status,
                'account_id' => $account_id,
                'host_id' => $host_id,
                'requested_permissions' => $requested_permissions,
                'created_timestamp' => $created_timestamp,
                'expires_timestamp' => $expires_timestamp
            ));

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                return $this->getAuthenticationRequest(AuthenticationRequestSearchMethod::requestToken, $request_token);
            }
        }

        /**
         * Returns an existing Authentication Request from the Database
         *
         * @param string $search_method
         * @param string $value
         * @return AuthenticationRequest
         * @throws AuthenticationRequestNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function getAuthenticationRequest(string $search_method, string $value): AuthenticationRequest
        {
            switch($search_method)
            {
                case AuthenticationRequestSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case AuthenticationRequestSearchMethod::requestToken:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('authentication_requests',[
                'id',
                'request_token',
                'application_id',
                'status',
                'account_id',
                'host_id',
                'requested_permissions',
                'created_timestamp',
                'expires_timestamp'
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
                    throw new AuthenticationRequestNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['requested_permissions'] = ZiProto::decode($Row['requested_permissions']);
                return AuthenticationRequest::fromArray($Row);
            }
        }

        /**
         * Updates an existing authentication request
         *
         * @param AuthenticationRequest $authenticationRequest
         * @return bool
         * @throws DatabaseException
         */
        public function updateAuthenticationRequest(AuthenticationRequest $authenticationRequest): bool
        {
            $id = (int)$authenticationRequest->Id;
            $request_token = $this->intellivoidAccounts->database->real_escape_string($authenticationRequest->RequestToken);
            $application_id = (int)$authenticationRequest->ApplicationId;
            $status = (int)$authenticationRequest->Status;
            $account_id = (int)$authenticationRequest->AccountId;
            $host_id = (int)$authenticationRequest->HostId;
            $expires = (int)$authenticationRequest->ExpiresTimestamp;

            $Query = QueryBuilder::update('authentication_requests', array(
                'id' => $id,
                'request_token' => $request_token,
                'application_id' => $application_id,
                'status' => $status,
                'account_id' => $account_id,
                'host_id' => $host_id,
                'expires_timestamp' => $expires
            ), 'id', (int)$authenticationRequest->Id);
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
    }