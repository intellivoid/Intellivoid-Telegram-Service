<?php


    namespace IntellivoidAccounts\Abstracts\SearchMethods;


    /**
     * Class ApplicationSearchMethod
     * @package IntellivoidAccounts\Abstracts\SearchMethods
     */
    abstract class ApplicationSearchMethod
    {
        const byId = 'id';

        const byApplicationId = 'public_app_id';

        const bySecretKey = 'secret_key';

        const byName = 'name';

        const byNameSafe = 'name_safe';
    }