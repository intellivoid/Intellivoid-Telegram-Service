<?php /** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Managers;


    use IntellivoidAccounts\Abstracts\AccountStatus;
    use IntellivoidAccounts\Abstracts\SearchMethods\AccountSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\ApplicationSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\SubscriptionPlanSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\SubscriptionPromotionSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\SubscriptionSearchMethod;
    use IntellivoidAccounts\Exceptions\AccountLimitedException;
    use IntellivoidAccounts\Exceptions\AccountNotFoundException;
    use IntellivoidAccounts\Exceptions\ApplicationNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InsufficientFundsException;
    use IntellivoidAccounts\Exceptions\InvalidAccountStatusException;
    use IntellivoidAccounts\Exceptions\InvalidEmailException;
    use IntellivoidAccounts\Exceptions\InvalidFundsValueException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\InvalidSubscriptionPromotionNameException;
    use IntellivoidAccounts\Exceptions\InvalidUsernameException;
    use IntellivoidAccounts\Exceptions\InvalidVendorException;
    use IntellivoidAccounts\Exceptions\SubscriptionNotFoundException;
    use IntellivoidAccounts\Exceptions\SubscriptionPlanNotFoundException;
    use IntellivoidAccounts\Exceptions\SubscriptionPromotionNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\Subscription;
    use IntellivoidAccounts\Utilities\Hashing;
    use msqg\QueryBuilder;
    use ZiProto\ZiProto;

    /**
     * Class SubscriptionManager
     * @package IntellivoidAccounts\Managers
     */
    class SubscriptionManager
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * SubscriptionManager constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
        }

        /**
         * Starts a new subscription for the account
         *
         * @param int $account_id
         * @param int $application_id
         * @param string $plan_name
         * @param string $promotion_code
         * @return Subscription
         * @throws AccountLimitedException
         * @throws AccountNotFoundException
         * @throws ApplicationNotFoundException
         * @throws DatabaseException
         * @throws InsufficientFundsException
         * @throws InvalidAccountStatusException
         * @throws InvalidEmailException
         * @throws InvalidFundsValueException
         * @throws InvalidSearchMethodException
         * @throws InvalidSubscriptionPromotionNameException
         * @throws InvalidUsernameException
         * @throws InvalidVendorException
         * @throws SubscriptionPlanNotFoundException
         * @throws SubscriptionPromotionNotFoundException
         * @throws SubscriptionNotFoundException
         */
        public function startSubscription(int $account_id, int $application_id, string $plan_name, string $promotion_code = "NONE"): Subscription
        {
            // Retrieve the required information
            $Application = $this->intellivoidAccounts->getApplicationManager()->getApplication(ApplicationSearchMethod::byId, $application_id);
            $Account = $this->intellivoidAccounts->getAccountManager()->getAccount(AccountSearchMethod::byId, $account_id);
            if($Account->Status == AccountStatus::Limited)
            {
                throw new AccountLimitedException();
            }

            $SubscriptionPlan = $this->intellivoidAccounts->getSubscriptionPlanManager()->getSubscriptionPlanByName(
                $application_id, $plan_name
            );


            $properties = new Subscription\Properties();
            $SubscriptionPromotion = null;

            if($promotion_code !== "NONE")
            {
                $SubscriptionPromotion = $this->intellivoidAccounts->getSubscriptionPromotionManager()->getSubscriptionPromotion(
                    SubscriptionPromotionSearchMethod::byPromotionCode, $promotion_code
                );
            }

            if(count($SubscriptionPlan->Features) > 0)
            {
                foreach($SubscriptionPlan->Features as $feature)
                {
                    $properties->addFeature($feature);
                }
            }

            if($SubscriptionPromotion == null)
            {
                $properties->InitialPrice = $SubscriptionPlan->InitialPrice;
                $properties->CyclePrice = $SubscriptionPlan->CyclePrice;
                $properties->PromotionID = 0;
            }
            else
            {
                $properties->InitialPrice = $SubscriptionPromotion->InitialPrice;
                $properties->CyclePrice = $SubscriptionPromotion->CyclePrice;
                $properties->PromotionID = $SubscriptionPromotion->ID;

                if(count($SubscriptionPromotion->Features) > 0)
                {
                    foreach($SubscriptionPromotion->Features as $feature)
                    {
                        $properties->addFeature($feature);
                    }
                }
            }

            $this->intellivoidAccounts->getTransactionManager()->processPayment(
                $account_id, $Application->Name . ' (' . $SubscriptionPlan->PlanName . ')',
                $properties->InitialPrice
            );

            if($SubscriptionPromotion->AffiliationAccountID !== 0)
            {
                if($SubscriptionPromotion->AffiliationInitialShare > 0)
                {

                    if($SubscriptionPromotion->AffiliationInitialShare > $properties->InitialPrice)
                    {
                        $SubscriptionPromotion->AffiliationInitialShare = $properties->InitialPrice;
                    }

                    $this->intellivoidAccounts->getTransactionManager()->addFunds(
                        $SubscriptionPromotion->AffiliationAccountID, $Application->Name . ' (' . $SubscriptionPlan->PlanName . ')',
                        $SubscriptionPromotion->AffiliationInitialShare
                    );
                }
            }

            $public_id = Hashing::SubscriptionPublicID($account_id, $SubscriptionPlan->ID);
            $public_id = $this->intellivoidAccounts->database->real_escape_string($public_id);
            $account_id = (int)$account_id;
            $subscription_plan_id = (int)$SubscriptionPlan->ID;
            $active = (int)True;
            $billing_cycle = (int)$SubscriptionPlan->BillingCycle;
            $next_billing_cycle = (int)time() + $billing_cycle;
            $properties = ZiProto::encode($properties->toArray());
            $properties = $this->intellivoidAccounts->database->real_escape_string($properties);
            $created_timestamp = (int)time();
            $flags = ZiProto::encode([]);
            $flags = $this->intellivoidAccounts->database->real_escape_string($flags);

            $Query = QueryBuilder::insert_into('subscriptions', array(
                'public_id' => $public_id,
                'account_id' => (int)$account_id,
                'subscription_plan_id' => (int)$subscription_plan_id,
                'active' => $active,
                'billing_cycle' => $billing_cycle,
                'next_billing_cycle' => $next_billing_cycle,
                'properties' => $properties,
                'created_timestamp' => $created_timestamp,
                'flags' => $flags
            ));
            $QueryResults = $this->intellivoidAccounts->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }

            return $this->getSubscription(SubscriptionSearchMethod::byPublicId, $public_id);
        }

        /**
         * Returns the subscription object from the database
         *
         * @param string $search_method
         * @param string $value
         * @return Subscription
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws SubscriptionNotFoundException
         */
        public function getSubscription(string $search_method, string $value): Subscription
        {
            switch($search_method)
            {
                case SubscriptionSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case SubscriptionSearchMethod::byPublicId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = $this->intellivoidAccounts->database->real_escape_string($value);
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('subscriptions', [
                'id',
                'public_id',
                'account_id',
                'subscription_plan_id',
                'active',
                'billing_cycle',
                'next_billing_cycle',
                'properties',
                'created_timestamp',
                'flags'
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
                    throw new SubscriptionNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['flags'] = ZiProto::decode($Row['flags']);
                $Row['properties'] = ZiProto::decode($Row['properties']);
                return Subscription::fromArray($Row);
            }
        }

        /**
         * Gets subscriptions associated with the Account ID
         *
         * @param int $account_id
         * @return array
         * @throws DatabaseException
         */
        public function getSubscriptionsByAccountID(int $account_id): array
        {
            $account_id = (int)$account_id;

            $Query = QueryBuilder::select('subscriptions', [
                'id',
                'public_id',
                'account_id',
                'subscription_plan_id',
                'active',
                'billing_cycle',
                'next_billing_cycle',
                'properties',
                'created_timestamp',
                'flags'
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
                    $Row['flags'] = ZiProto::decode($Row['flags']);
                    $Row['properties'] = ZiProto::decode($Row['properties']);
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }

        /**
         * Determines if the Subscription Plan is associated with an account
         *
         * @param int $account_id
         * @param int $subscription_plan_id
         * @return bool
         * @throws DatabaseException
         */
        public function subscriptionPlanAssociatedWithAccount(int $account_id, int $subscription_plan_id): bool
        {
            $account_id = (int)$account_id;
            $subscription_plan_id = (int)$subscription_plan_id;

            $Query = QueryBuilder::select('subscriptions', ['id'],
                'account_id', $account_id . "' AND subscription_plan_id='$subscription_plan_id"
            );
            $QueryResults = $this->intellivoidAccounts->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    return false;
                }

                return true;
            }
        }

        /**
         * Gets a subscription plan associated with an account
         *
         * @param int $account_id
         * @param int $subscription_plan_id
         * @return Subscription
         * @throws DatabaseException
         * @throws SubscriptionNotFoundException
         */
        public function getSubscriptionPlanAssociatedWithAccount(int $account_id, int $subscription_plan_id): Subscription
        {
            $account_id = (int)$account_id;

            $Query = QueryBuilder::select('subscriptions', [
                'id',
                'public_id',
                'account_id',
                'subscription_plan_id',
                'active',
                'billing_cycle',
                'next_billing_cycle',
                'properties',
                'created_timestamp',
                'flags'
            ], 'account_id', $account_id . "' AND subscription_plan_id='$subscription_plan_id");
            $QueryResults = $this->intellivoidAccounts->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new SubscriptionNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['flags'] = ZiProto::decode($Row['flags']);
                $Row['properties'] = ZiProto::decode($Row['properties']);
                return Subscription::fromArray($Row);
            }
        }

        /**
         * Updates an existing subscription from the database
         *
         * @param Subscription $subscription
         * @return bool
         * @throws DatabaseException
         */
        public function updateSubscription(Subscription $subscription): bool
        {
            $id = (int)$subscription->ID;
            $active = (int)$subscription->Active;
            $billing_cycle = (int)$subscription->BillingCycle;
            $next_billing_cycle = (int)$subscription->NextBillingCycle;
            $properties = ZiProto::encode($subscription->Properties->toArray());
            $properties = $this->intellivoidAccounts->database->real_escape_string($properties);
            $flags = ZiProto::encode($subscription->Flags);
            $flags = $this->intellivoidAccounts->database->real_escape_string($flags);

            $Query = QueryBuilder::update('subscriptions',array(
                'active' => $active,
                'billing_cycle' => $billing_cycle,
                'next_billing_cycle' => $next_billing_cycle,
                'properties' => $properties,
                'flags' => $flags
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

        /**
         * Processes the billing for the subscription if applicable
         *
         * @param Subscription $subscription
         * @return bool
         * @throws AccountNotFoundException
         * @throws ApplicationNotFoundException
         * @throws DatabaseException
         * @throws InsufficientFundsException
         * @throws InvalidAccountStatusException
         * @throws InvalidEmailException
         * @throws InvalidFundsValueException
         * @throws InvalidSearchMethodException
         * @throws InvalidSubscriptionPromotionNameException
         * @throws InvalidUsernameException
         * @throws InvalidVendorException
         * @throws SubscriptionPlanNotFoundException
         * @throws SubscriptionPromotionNotFoundException
         */
        public function processBilling(Subscription $subscription): bool
        {
            if($subscription->NextBillingCycle > (int)time())
            {
                return False;
            }

            $SubscriptionPlan = $this->intellivoidAccounts->getSubscriptionPlanManager()->getSubscriptionPlan(
                SubscriptionPlanSearchMethod::byId, $subscription->SubscriptionPlanID
            );
            $Application = $this->intellivoidAccounts->getApplicationManager()->getApplication(
                ApplicationSearchMethod::byId, $SubscriptionPlan->ApplicationID
            );

            $this->intellivoidAccounts->getTransactionManager()->processPayment(
                $subscription->AccountID, $Application->Name . ' (' . $SubscriptionPlan->PlanName . ')',
                $subscription->Properties->CyclePrice
            );

            if($subscription->Properties->PromotionID !== 0)
            {
                $SubscriptionPromotion = $this->intellivoidAccounts->getSubscriptionPromotionManager()->getSubscriptionPromotion(
                    SubscriptionPromotionSearchMethod::byId, $subscription->Properties->PromotionID
                );

                if($SubscriptionPromotion->AffiliationAccountID !== 0)
                {
                    if($SubscriptionPromotion->AffiliationCycleShare > 0)
                    {
                        if($SubscriptionPromotion->CyclePrice >  $SubscriptionPlan->CyclePrice)
                        {
                            $SubscriptionPromotion->CyclePrice = $SubscriptionPlan->CyclePrice;
                        }

                        $this->intellivoidAccounts->getTransactionManager()->addFunds(
                            $SubscriptionPromotion->AffiliationAccountID, $Application->Name . ' (' . $SubscriptionPlan->PlanName . ')',
                            $SubscriptionPromotion->AffiliationInitialShare
                        );
                    }
                }
            }

            return True;
        }

        /**
         * Cancels an existing subscription
         *
         * @param Subscription $subscription
         * @return bool
         * @throws DatabaseException
         */
        public function cancelSubscription(Subscription $subscription)
        {
            $id = (int)$subscription->ID;

            $Query = "DELETE FROM `subscriptions` WHERE id=$id";
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