<?php /** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\SearchMethods\AccountSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\SubscriptionPromotionSearchMethod;
    use IntellivoidAccounts\Abstracts\SubscriptionPromotionStatus;
    use IntellivoidAccounts\Exceptions\AccountNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidCyclePriceException;
    use IntellivoidAccounts\Exceptions\InvalidCyclePriceShareException;
    use IntellivoidAccounts\Exceptions\InvalidFeatureException;
    use IntellivoidAccounts\Exceptions\InvalidInitialPriceException;
    use IntellivoidAccounts\Exceptions\InvalidInitialPriceShareException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\InvalidSubscriptionPromotionNameException;
    use IntellivoidAccounts\Exceptions\SubscriptionPromotionAlreadyExistsException;
    use IntellivoidAccounts\Exceptions\SubscriptionPromotionNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\Subscription\Feature;
    use IntellivoidAccounts\Objects\SubscriptionPromotion;
    use IntellivoidAccounts\Utilities\Converter;
    use IntellivoidAccounts\Utilities\Hashing;
    use IntellivoidAccounts\Utilities\Validate;
    use msqg\QueryBuilder;
    use ZiProto\ZiProto;

    /**
     * Class SubscriptionPromotionManager
     * @package IntellivoidAccounts\Managers
     */
    class SubscriptionPromotionManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * SubscriptionPromotionManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Creates a new subscription promotion record in the database
         *
         * @param int $subscription_plan_id
         * @param string $promotion_code
         * @param float $initial_price
         * @param float $cycle_price
         * @param int $affiliation_account_id
         * @param float $affiliation_initial_share
         * @param float $affiliation_cycle_share
         * @param array $features
         * @return SubscriptionPromotion
         * @throws AccountNotFoundException
         * @throws DatabaseException
         * @throws InvalidCyclePriceException
         * @throws InvalidCyclePriceShareException
         * @throws InvalidFeatureException
         * @throws InvalidInitialPriceException
         * @throws InvalidInitialPriceShareException
         * @throws InvalidSearchMethodException
         * @throws InvalidSubscriptionPromotionNameException
         * @throws SubscriptionPromotionAlreadyExistsException
         * @throws SubscriptionPromotionNotFoundException
         */
        public function createSubscriptionPromotion(int $subscription_plan_id, string $promotion_code, float $initial_price, float $cycle_price, int $affiliation_account_id, float $affiliation_initial_share, float $affiliation_cycle_share, array $features): SubscriptionPromotion
        {
            $promotion_code = Converter::subscriptionPromotionCode($promotion_code);
            if(Validate::subscriptionPromotionCode($promotion_code) == false)
            {
                throw new InvalidSubscriptionPromotionNameException();
            }

            try
            {
                $this->getSubscriptionPromotion(SubscriptionPromotionSearchMethod::byPromotionCode, $promotion_code);
                throw new SubscriptionPromotionAlreadyExistsException();
            }
            catch(SubscriptionPromotionNotFoundException $e)
            {
                unset($e);
            }

            if($initial_price < 0)
            {
                throw new InvalidInitialPriceException();
            }

            if($cycle_price < 0)
            {
                throw new InvalidCyclePriceException();
            }

            if($affiliation_account_id == 0)
            {
                $affiliation_initial_share = (float)0;
                $affiliation_cycle_share = (float)0;
            }
            else
            {
                $this->intellivoidAccounts->getAccountManager()->getAccount(AccountSearchMethod::byId, $affiliation_account_id);

                if($affiliation_cycle_share < 0)
                {
                    throw new InvalidCyclePriceShareException();
                }

                if($affiliation_cycle_share > $cycle_price)
                {
                    throw new InvalidCyclePriceShareException();
                }

                if($affiliation_initial_share < 0)
                {
                    throw new InvalidInitialPriceShareException();
                }

                if($affiliation_initial_share > $initial_price)
                {
                    throw new InvalidInitialPriceShareException();
                }
            }

            $public_id = Hashing::SubscriptionPromotionPublicID((int)$subscription_plan_id, $promotion_code);
            /** @var Feature $feature */
            foreach($features as $feature)
            {
                if(is_null($feature->Name))
                {
                    throw new InvalidFeatureException();
                }

                if(is_null($feature->Value))
                {
                    throw new InvalidFeatureException();
                }
            }

            $decoded_features = array();
            /** @var Feature $feature */
            foreach($features as $feature)
            {
                $decoded_features[] = $feature->toArray();
            }

            $decoded_features = ZiProto::encode($decoded_features);
            $decoded_features = $this->intellivoidAccounts->database->real_escape_string($decoded_features);
            $public_id = $this->intellivoidAccounts->database->real_escape_string($public_id);
            $promotion_code = $this->intellivoidAccounts->database->real_escape_string($promotion_code);
            $flags = ZiProto::encode([]);
            $flags = $this->intellivoidAccounts->database->real_escape_string($flags);
            $last_updated_timestamp = (int)time();
            $created_timestamp = $last_updated_timestamp;

            $Query = QueryBuilder::insert_into('subscription_promotions', array(
                'public_id' => $public_id,
                'promotion_code' => $promotion_code,
                'subscription_plan_id' => (int)$subscription_plan_id,
                'initial_price' => (float)$initial_price,
                'cycle_price' => (float)$cycle_price,
                'affiliation_account_id' => (int)$affiliation_account_id,
                'affiliation_initial_share' => (float)$affiliation_initial_share,
                'affiliation_cycle_share' => (float)$affiliation_cycle_share,
                'features' => $decoded_features,
                'status' => (int)SubscriptionPromotionStatus::Active,
                'flags' => $flags,
                'last_updated_timestamp' => $last_updated_timestamp,
                'created_timestamp' => $created_timestamp
            ));

            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }

            return $this->getSubscriptionPromotion(SubscriptionPromotionSearchMethod::byPublicId, $public_id);
        }

        /**
         * Returns the subscription promotion from the database
         *
         * @param string $search_method
         * @param string $value
         * @return SubscriptionPromotion
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws InvalidSubscriptionPromotionNameException
         * @throws SubscriptionPromotionNotFoundException
         */
        public function getSubscriptionPromotion(string $search_method, string $value): SubscriptionPromotion
        {
            switch($search_method)
            {
                case SubscriptionPromotionSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case SubscriptionPromotionSearchMethod::byPublicId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                case SubscriptionPromotionSearchMethod::byPromotionCode:
                    if(Validate::subscriptionPromotionCode($value) == false)
                    {
                        throw new InvalidSubscriptionPromotionNameException();
                    }
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = Converter::subscriptionPromotionCode($value);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('subscription_promotions', [
                'id',
                'public_id',
                'promotion_code',
                'subscription_plan_id',
                'initial_price',
                'cycle_price',
                'affiliation_account_id',
                'affiliation_initial_share',
                'affiliation_cycle_share',
                'features',
                'status',
                'flags',
                'last_updated_timestamp',
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
                    throw new SubscriptionPromotionNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['features'] = ZiProto::decode($Row['features']);
                $Row['flags'] = ZiProto::decode($Row['flags']);
                return SubscriptionPromotion::fromArray($Row);
            }
        }

        /**
         * Updates an existing subscription promotion record in the database
         *
         * @param SubscriptionPromotion $subscriptionPromotion
         * @return bool
         * @throws DatabaseException
         * @throws InvalidCyclePriceShareException
         * @throws InvalidInitialPriceShareException
         * @throws InvalidSearchMethodException
         * @throws InvalidSubscriptionPromotionNameException
         * @throws SubscriptionPromotionNotFoundException
         */
        public function updateSubscriptionPromotion(SubscriptionPromotion $subscriptionPromotion): bool
        {
            $this->getSubscriptionPromotion(SubscriptionPromotionSearchMethod::byId, $subscriptionPromotion->ID);

            $promotion_code = Converter::subscriptionPromotionCode($subscriptionPromotion->PromotionCode);
            if(Validate::subscriptionPromotionCode($subscriptionPromotion->PromotionCode) == false)
            {
                throw new InvalidSubscriptionPromotionNameException();
            }

            $promotion_code = $this->intellivoidAccounts->database->real_escape_string($promotion_code);
            $public_id = Hashing::SubscriptionPromotionPublicID($subscriptionPromotion->SubscriptionPlanID, $promotion_code);
            $initial_price = (float)$subscriptionPromotion->InitialPrice;
            $cycle_price = (float)$subscriptionPromotion->CyclePrice;
            $affiliation_account_id = (int)$subscriptionPromotion->AffiliationAccountID;
            $affiliation_initial_share = (float)$subscriptionPromotion->AffiliationInitialShare;
            $affiliation_cycle_share = (float)$subscriptionPromotion->AffiliationCycleShare;

            if($subscriptionPromotion->AffiliationAccountID == 0)
            {
                $affiliation_initial_share = (float)0;
                $affiliation_cycle_share = (float)0;
            }
            else
            {
                if($affiliation_cycle_share < 0)
                {
                    throw new InvalidCyclePriceShareException();
                }

                if($affiliation_cycle_share > $cycle_price)
                {
                    throw new InvalidCyclePriceShareException();
                }

                if($affiliation_initial_share < 0)
                {
                    throw new InvalidInitialPriceShareException();
                }

                if($affiliation_initial_share > $initial_price)
                {
                    throw new InvalidInitialPriceShareException();
                }
            }


            $decoded_features = array();
            /** @var Feature $feature */
            foreach($subscriptionPromotion->Features as $feature)
            {
                $decoded_features[] = $feature->toArray();
            }

            $decoded_features = $this->intellivoidAccounts->database->real_escape_string(ZiProto::encode($decoded_features));
            $flags = $this->intellivoidAccounts->database->real_escape_string(ZiProto::encode($subscriptionPromotion->Flags));
            $last_updated_timestamp = (int)time();
            $status = (int)$subscriptionPromotion->Status;

            $Query = QueryBuilder::update('subscription_promotions', array(
                'public_id' => $public_id,
                'promotion_code' => $promotion_code,
                'initial_price' => $initial_price,
                'cycle_price' => $cycle_price,
                'affiliation_account_id' => $affiliation_account_id,
                'affiliation_initial_share' => $affiliation_initial_share,
                'affiliation_cycle_share' => $affiliation_cycle_share,
                'features' => $decoded_features,
                'status' => $status,
                'flags' => $flags,
                'last_updated_timestamp' => $last_updated_timestamp
            ), 'id', (int)$subscriptionPromotion->ID);
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
         * Deletes an subscription promotion from the database
         *
         * @param SubscriptionPromotion $subscriptionPromotion
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws InvalidSubscriptionPromotionNameException
         * @throws SubscriptionPromotionNotFoundException
         */
        public function deleteSubscriptionPromotion(SubscriptionPromotion $subscriptionPromotion): bool
        {
            $this->getSubscriptionPromotion(SubscriptionPromotionSearchMethod::byId, $subscriptionPromotion->ID);
            $id = (int)$subscriptionPromotion->ID;

            $Query = "DELETE FROM `subscription_promotions` WHERE id=$id";
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