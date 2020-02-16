<?php


    namespace IntellivoidAccounts\Managers;


    use BasicCalculator\BC;
    use IntellivoidAccounts\Abstracts\SearchMethods\AccountSearchMethod;
    use IntellivoidAccounts\Exceptions\AccountNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InsufficientFundsException;
    use IntellivoidAccounts\Exceptions\InvalidAccountStatusException;
    use IntellivoidAccounts\Exceptions\InvalidEmailException;
    use IntellivoidAccounts\Exceptions\InvalidFundsValueException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\InvalidUsernameException;
    use IntellivoidAccounts\Exceptions\InvalidVendorException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\Account;
    use IntellivoidAccounts\Utilities\Validate;

    /**
     * Class TransactionManager
     * @package IntellivoidAccounts\Managers
     */
    class TransactionManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * TransactionManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Adds funds to an account
         *
         * @param int $account_id
         * @param string $vendor
         * @param float $amount
         * @return bool
         * @throws AccountNotFoundException
         * @throws DatabaseException
         * @throws InvalidFundsValueException
         * @throws InvalidSearchMethodException
         * @throws InvalidVendorException
         * @throws InvalidAccountStatusException
         * @throws InvalidEmailException
         * @throws InvalidUsernameException
         */
        public function addFunds(int $account_id, string $vendor, float $amount): bool
        {
            if(Validate::vendor($vendor) == false)
            {
                throw new InvalidVendorException();
            }

            if($amount < 0)
            {
                throw new InvalidFundsValueException();
            }

            $Account = $this->intellivoidAccounts->getAccountManager()->getAccount(AccountSearchMethod::byId, $account_id);
            $Account->Configuration->Balance = (float)BC::add($Account->Configuration->Balance, abs($amount), 2);
            $this->intellivoidAccounts->getAccountManager()->updateAccount($Account);
            $this->intellivoidAccounts->getTransactionRecordManager()->logTransaction(
                $Account->ID, $vendor, $amount
            );
            
            return True;
        }

        /**
         * Processes a payment from the account
         *
         * @param int $account_id
         * @param string $vendor
         * @param float $amount
         * @return bool
         * @throws AccountNotFoundException
         * @throws DatabaseException
         * @throws InsufficientFundsException
         * @throws InvalidAccountStatusException
         * @throws InvalidEmailException
         * @throws InvalidFundsValueException
         * @throws InvalidSearchMethodException
         * @throws InvalidUsernameException
         * @throws InvalidVendorException
         */
        public function processPayment(int $account_id, string $vendor, float $amount): bool
        {
            if(Validate::vendor($vendor) == false)
            {
                throw new InvalidVendorException();
            }

            if($amount < 0)
            {
                throw new InvalidFundsValueException();
            }

            $Account = $this->intellivoidAccounts->getAccountManager()->getAccount(AccountSearchMethod::byId, $account_id);
            $Account->Configuration->Balance = (float)BC::sub($Account->Configuration->Balance, abs($amount), 2);

            if($Account->Configuration->Balance < 0)
            {
                throw new InsufficientFundsException();
            }

            $this->intellivoidAccounts->getAccountManager()->updateAccount($Account);
            $this->intellivoidAccounts->getTransactionRecordManager()->logTransaction(
                $Account->ID, $vendor, -$amount
            );

            return True;
        }

        /**
         * Validates if the subtraction operation is valid
         *
         * @param Account $account
         * @param string $vendor
         * @param float $amount
         * @return bool
         * @throws InsufficientFundsException
         * @throws InvalidFundsValueException
         * @throws InvalidVendorException
         */
        public function validateSubOperation(Account $account, string $vendor, float $amount): bool
        {
            if(Validate::vendor($vendor) == false)
            {
                throw new InvalidVendorException();
            }

            if($amount < 0)
            {
                throw new InvalidFundsValueException();
            }

            $account->Configuration->Balance = (float)BC::sub($account->Configuration->Balance, abs($amount), 2);

            if($account->Configuration->Balance < 0)
            {
                throw new InsufficientFundsException();
            }

            return True;
        }

        /**
         * Transfer funds from one account to another
         *
         * @param int $from_id
         * @param int $to_id
         * @param float $amount
         * @return bool
         * @throws AccountNotFoundException
         * @throws DatabaseException
         * @throws InsufficientFundsException
         * @throws InvalidAccountStatusException
         * @throws InvalidEmailException
         * @throws InvalidFundsValueException
         * @throws InvalidSearchMethodException
         * @throws InvalidUsernameException
         * @throws InvalidVendorException
         */
        public function transferFunds(int $from_id, int $to_id, float $amount): bool
        {
            $from_account = $this->intellivoidAccounts->getAccountManager()->getAccount(
                AccountSearchMethod::byId, $from_id
            );

            $this->validateSubOperation($from_account, $from_account->Username, $amount);

            $to_account = $this->intellivoidAccounts->getAccountManager()->getAccount(
                AccountSearchMethod::byId, $to_id
            );

            $this->processPayment($from_id, $to_account->Username, $amount);
            $this->addFunds($to_id, $from_account->Username, $amount);

            return True;
        }
    }