<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class AuthenticationAccessStatus
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class AuthenticationAccessStatus
    {
        const Active = 0;

        const Revoked = 1;
    }