<?php


    namespace IntellivoidAccounts\Managers;

    use IntellivoidAccounts\Abstracts\SearchMethods\TransactionLogSearchMethod;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\TransactionRecordNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\TransactionRecord;
    use IntellivoidAccounts\Utilities\Hashing;
    use msqg\Abstracts\SortBy;
    use msqg\QueryBuilder;

    /**
     * Class TransactionRecordManager
     * @package IntellivoidAccounts\Managers
     */
    class TransactionRecordManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * TransactionRecordManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Logs the transaction
         *
         * @param int $account_id
         * @param string $vendor
         * @param float $amount
         * @return bool
         * @throws DatabaseException
         */
        public function logTransaction(int $account_id, string $vendor, float $amount): bool
        {
            $timestamp = (int)time();
            $public_id = Hashing::TransactionRecordPublicID($account_id, $vendor, $timestamp);
            $account_id = (int)$account_id;
            $vendor = $this->intellivoidAccounts->database->real_escape_string($vendor);;
            $amount = (float)$amount;

            $Query = QueryBuilder::insert_into('transaction_records', array(
                'public_id' => $public_id,
                'account_id' => $account_id,
                'vendor' => $vendor,
                'amount' => $amount,
                'timestamp' => $timestamp
            ));
            $QueryResults = $this->intellivoidAccounts->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }

            return true;
        }

        /**
         * Gets an existing Transaction Record from the database
         *
         * @param string $search_method
         * @param $value
         * @return TransactionRecord
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws TransactionRecordNotFoundException
         */
        public function getTransactionRecord(string $search_method, $value): TransactionRecord
        {
            switch($search_method)
            {
                case TransactionLogSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case TransactionLogSearchMethod::byPublicId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('transaction_records', [
                'id',
                'public_id',
                'account_id',
                'vendor',
                'amount',
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
                    throw new TransactionRecordNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                return TransactionRecord::fromArray($Row);
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
            $Query = "SELECT COUNT(id) AS total FROM `transaction_records` WHERE account_id=$account_id";

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
         * Returns an array of Transaction Records
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

            $Query = QueryBuilder::select('transaction_records', [
                'id',
                'public_id',
                'account_id',
                'vendor',
                'amount',
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
         * Returns the newer recent transaction records
         *
         * @param int $account_id
         * @param int $limit
         * @return array
         * @throws DatabaseException
         */
        public function getNewRecords(int $account_id, $limit = 50): array
        {
            $account_id = (int)$account_id;

            $Query = QueryBuilder::select('transaction_records', [
                'id',
                'public_id',
                'account_id',
                'vendor',
                'amount',
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