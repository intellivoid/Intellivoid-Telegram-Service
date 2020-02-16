<?php


    namespace IntellivoidAccounts\Abstracts\SearchMethods;


    /**
     * Class SubscriptionPromotionSearchMethod
     * @package IntellivoidAccounts\Abstracts\SearchMethods
     */
    abstract class SubscriptionPromotionSearchMethod
    {
        const byId = 'id';

        const byPublicId = 'public_id';

        const byPromotionCode = 'promotion_code';
    }