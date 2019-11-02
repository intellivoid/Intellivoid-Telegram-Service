<?php


    namespace IntellivoidAccounts\Abstracts\SearchMethods;

    /**
     * Class KnownHostsSearchMethod
     * @package IntellivoidAccounts\Abstracts\SearchMethods
     */
    abstract class KnownHostsSearchMethod
    {
        const byId = 'id';

        const byPublicId = 'public_id';

        const byIpAddress = 'ip_address';
    }