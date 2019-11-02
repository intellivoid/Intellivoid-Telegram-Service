<?php


    namespace IntellivoidAccounts\Abstracts\SearchMethods;


    /**
     * Class AuthenticationAccessSearchMethod
     * @package IntellivoidAccounts\Abstracts\SearchMethods
     */
    abstract class AuthenticationAccessSearchMethod
    {
        const byId = 'id';

        const byAccessToken = 'access_token';

        const byRequestId = 'request_id';
    }