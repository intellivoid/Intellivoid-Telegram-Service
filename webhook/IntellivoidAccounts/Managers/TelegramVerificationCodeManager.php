<?php


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\SearchMethods\TelegramVerificationCodeSearchMethod;
    use IntellivoidAccounts\Abstracts\TelegramVerificationCodeStatus;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\TelegramVerificationCodeNotFound;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\TelegramVerificationCode;
    use IntellivoidAccounts\Utilities\Hashing;
    use msqg\QueryBuilder;

    /**
     * Class TelegramVerificationCodeManager
     * @package IntellivoidAccounts\Managers
     */
    class TelegramVerificationCodeManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * TelegramVerificationCodeManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Generates a verification code that will last for 5 minutes.
         *
         * @param int $telegram_client_id
         * @return TelegramVerificationCode
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws TelegramVerificationCodeNotFound
         */
        public function generateCode(int $telegram_client_id): TelegramVerificationCode
        {
            $time = (int)time();
            $verification_code = Hashing::telegramVerificationCode($telegram_client_id, $time);
            $verification_code = $this->intellivoidAccounts->database->real_escape_string($verification_code);
            $status = (int)TelegramVerificationCodeStatus::Active;
            $expires = $time + 300;
            $created = $time;

            $Query = QueryBuilder::insert_into('telegram_verification_codes', array(
                'verification_code' => $verification_code,
                'telegram_client_id' => (int)$telegram_client_id,
                'status' => $status,
                'expires' => $expires,
                'created' => $created
            ));

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                return $this->getVerificationCode(TelegramVerificationCodeSearchMethod::byVerificationCode, $verification_code);
            }
        }

        /**
         * Gets an existing Verification Code from the Database
         *
         * @param string $search_method
         * @param string $value
         * @return TelegramVerificationCode
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws TelegramVerificationCodeNotFound
         */
        public function getVerificationCode(string $search_method, string $value): TelegramVerificationCode
        {
            switch($search_method)
            {
                case TelegramVerificationCodeSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case TelegramVerificationCodeSearchMethod::byVerificationCode:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('telegram_verification_codes', [
                'id',
                'verification_code',
                'telegram_client_id',
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
                    throw new TelegramVerificationCodeNotFound();
                }

                return TelegramVerificationCode::fromArray($QueryResults->fetch_array(MYSQLI_ASSOC));
            }
        }

        /**
         * Updates an existing Verification Code on the Database
         *
         * @param TelegramVerificationCode $telegramVerificationCode
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws TelegramVerificationCodeNotFound
         */
        public function updateVerificationCode(TelegramVerificationCode $telegramVerificationCode): bool
        {
            $this->getVerificationCode(TelegramVerificationCodeSearchMethod::byId, $telegramVerificationCode->ID);

            $id = (int)$telegramVerificationCode->ID;
            $verification_code = $this->intellivoidAccounts->database->real_escape_string($telegramVerificationCode->VerificationCode);
            $telegram_client_id = (int)$telegramVerificationCode->TelegramClientID;
            $status = (int)$telegramVerificationCode->Status;
            $expires = (int)$telegramVerificationCode->Expires;

            $Query = QueryBuilder::update('telegram_verification_codes', array(
                'verification_code' => $verification_code,
                'telegram_client_id' => $telegram_client_id,
                'status' => $status,
                'expires' => $expires
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