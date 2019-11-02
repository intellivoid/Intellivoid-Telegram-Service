<?php /** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Managers;

    use IntellivoidAccounts\Abstracts\AuthenticationAccessStatus;
    use IntellivoidAccounts\Abstracts\SearchMethods\AuthenticationAccessSearchMethod;
    use IntellivoidAccounts\Exceptions\AuthenticationAccessNotFoundException;
    use IntellivoidAccounts\Exceptions\AuthenticationRequestAlreadyUsedException;
    use IntellivoidAccounts\Exceptions\AuthenticationRequestNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\COA\AuthenticationAccess;
    use IntellivoidAccounts\Objects\COA\AuthenticationRequest;
    use IntellivoidAccounts\Utilities\Hashing;
    use msqg\QueryBuilder;
    use ZiProto\ZiProto;

    /**
     * Class AuthenticationAccessManager
     * @package IntellivoidAccounts\Managers
     */
    class AuthenticationAccessManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * AuthenticationAccessManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Creates a new Authentication Access Token
         *
         * @param AuthenticationRequest $authenticationRequest
         * @return AuthenticationAccess
         * @throws AuthenticationAccessNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws AuthenticationRequestAlreadyUsedException
         * @throws AuthenticationRequestNotFoundException
         */
        public function createAuthenticationAccess(AuthenticationRequest $authenticationRequest): AuthenticationAccess
        {
            try
            {
                $this->getAuthenticationAccess(AuthenticationAccessSearchMethod::byRequestId, $authenticationRequest->Id);
                throw new AuthenticationRequestAlreadyUsedException();
            }
            catch(AuthenticationAccessNotFoundException $authenticationAccessNotFoundException)
            {
                unset($authenticationAccessNotFoundException);
            }

            $originalAuthenticationRequest = $this->intellivoidAccounts->getCrossOverAuthenticationManager()->getAuthenticationRequestManager()->getAuthenticationRequest(AuthenticationAccessSearchMethod::byId, $authenticationRequest->Id);
            $originalAuthenticationRequest->AccountId = $authenticationRequest->AccountId;

            $current_timestamp = (int)time();
            $access_token = Hashing::authenticationAccessToken(
                $authenticationRequest->Id,
                $authenticationRequest->RequestToken,
                $current_timestamp,
                $authenticationRequest->AccountId,
                $authenticationRequest->HostId
            );
            $access_token = $this->intellivoidAccounts->database->real_escape_string($access_token);
            $application_id = (int)$authenticationRequest->ApplicationId;
            $account_id = (int)$authenticationRequest->AccountId;
            $request_id = (int)$authenticationRequest->Id;
            $permissions = $authenticationRequest->RequestedPermissions;
            $permissions = ZiProto::encode($permissions);
            $status = (int)AuthenticationAccessStatus::Active;
            $expires_timestamp = $current_timestamp + 43200;
            $last_used_timestamp = $current_timestamp;
            $created_timestamp = $current_timestamp;

            $Query = QueryBuilder::insert_into('authentication_access', array(
                'access_token' => $access_token,
                'application_id' => $application_id,
                'account_id' => $account_id,
                'request_id' => $request_id,
                'permissions' => $permissions,
                'status' => $status,
                'expires_timestamp' => $expires_timestamp,
                'last_used_timestamp' => $last_used_timestamp,
                'created_timestamp' => $created_timestamp
            ));

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                $this->intellivoidAccounts->getCrossOverAuthenticationManager()->getAuthenticationRequestManager()->updateAuthenticationRequest($originalAuthenticationRequest);
                return $this->getAuthenticationAccess(AuthenticationAccessSearchMethod::byAccessToken, $access_token);
            }
        }

        /**
         * Returns an existing AuthenticationAccess record from the database
         *
         * @param string $search_method
         * @param string $value
         * @return AuthenticationAccess
         * @throws AuthenticationAccessNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function getAuthenticationAccess(string $search_method, string $value): AuthenticationAccess
        {
            switch($search_method)
            {
                case AuthenticationAccessSearchMethod::byRequestId:
                case AuthenticationAccessSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case AuthenticationAccessSearchMethod::byAccessToken:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('authentication_access', [
                'id',
                'access_token',
                'application_id',
                'account_id',
                'request_id',
                'permissions',
                'status',
                'expires_timestamp',
                'last_used_timestamp',
                'created_timestamp'
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
                    throw new AuthenticationAccessNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['permissions'] = ZiProto::decode($Row['permissions']);
                return AuthenticationAccess::fromArray($Row);
            }
        }

        /**
         * Updates an existing Authentication Access token in the database
         *
         * @param AuthenticationAccess $authenticationAccess
         * @return bool
         * @throws AuthenticationAccessNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function updateAuthenticationAccess(AuthenticationAccess $authenticationAccess): bool
        {
            $this->getAuthenticationAccess(AuthenticationAccessSearchMethod::byId, (int)$authenticationAccess->ID);

            $id = (int)$authenticationAccess->ID;
            $access_token = $this->intellivoidAccounts->database->real_escape_string($authenticationAccess->AccessToken);
            $application_id = (int)$authenticationAccess->ApplicationId;
            $account_id = (int)$authenticationAccess->AccountId;
            $request_id = (int)$authenticationAccess->RequestId;
            $status = (int)$authenticationAccess->Status;
            $expires_timestamp = (int)$authenticationAccess->ExpiresTimestamp;
            $last_used_timestamp = (int)$authenticationAccess->LastUsedTimestamp;

            $Query = QueryBuilder::update('authentication_access', array(
                'access_token' => $access_token,
                'application_id' => $application_id,
                'account_id' => $account_id,
                'request_id' => $request_id,
                'status' => $status,
                'expires_timestamp' => $expires_timestamp,
                'last_used_timestamp' => $last_used_timestamp
            ), 'id', $id);
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