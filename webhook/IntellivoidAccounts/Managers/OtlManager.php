<?php


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\OtlStatus;
    use IntellivoidAccounts\Abstracts\SearchMethods\OtlSearchMethod;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\InvalidVendorException;
    use IntellivoidAccounts\Exceptions\OtlNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\OneTimeLoginCode;
    use IntellivoidAccounts\Utilities\Hashing;
    use IntellivoidAccounts\Utilities\Validate;
    use msqg\QueryBuilder;

    /**
     * Class OtlManager
     * @package IntellivoidAccounts\Managers
     */
    class OtlManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * OtlManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Generates a one-time login code for the given account ID
         *
         * @param int $account_id
         * @return string
         * @throws DatabaseException
         */
        public function generateLoginCode(int $account_id): string
        {
            $created_timestamp = (int)time();
            $expires_timestamp = $created_timestamp + 300;

            $code = Hashing::OneTimeLoginCode($account_id, $created_timestamp, $expires_timestamp);
            $code = $this->intellivoidAccounts->database->real_escape_string($code);
            $account_id = (int)$account_id;
            $status = (int)OtlStatus::Available;
            $vendor = 'None';

            $Query = QueryBuilder::insert_into('otl_codes', array(
                'code' => $code,
                'vendor' => $vendor,
                'account_id' => $account_id,
                'status' => $status,
                'expires' => $expires_timestamp,
                'created' => $created_timestamp
            ));
            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                return $code;
            }
        }

        /**
         * Gets an existing OtlRecord from the database
         *
         * @param string $search_method
         * @param string $value
         * @return OneTimeLoginCode
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws OtlNotFoundException
         */
        public function getOtlRecord(string $search_method, string $value): OneTimeLoginCode
        {
            switch($search_method)
            {
                case OtlSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case OtlSearchMethod::byCode:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('otl_codes', [
                'id',
                'code',
                'vendor',
                'account_id',
                'status',
                'expires',
                'created'
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
                    throw new OtlNotFoundException();
                }

                return OneTimeLoginCode::fromArray($QueryResults->fetch_array(MYSQLI_ASSOC));
            }
        }

        /**
         * Updates an existing OtlRecord
         *
         * @param OneTimeLoginCode $oneTimeLoginCode
         * @param bool $check
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws InvalidVendorException
         * @throws OtlNotFoundException
         */
        public function updateOtlRecord(OneTimeLoginCode $oneTimeLoginCode, bool $check = false): bool
        {
            if($check)
            {
                $this->getOtlRecord(OtlSearchMethod::byId, $oneTimeLoginCode->ID);
            }

            if(Validate::vendor($oneTimeLoginCode->Vendor) == false)
            {
                throw new InvalidVendorException();
            }

            $id = (int)$oneTimeLoginCode->ID;
            $vendor = $this->intellivoidAccounts->database->real_escape_string($oneTimeLoginCode->Vendor);
            $status = (int)$oneTimeLoginCode->Status;

            $Query = QueryBuilder::update('otl_codes', array(
                'vendor' => $vendor,
                'status' => $status
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