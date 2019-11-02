<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class AuthenticationRequestStatus
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class AuthenticationRequestStatus
    {
        const Active = 0;

        const Blocked = 1;
    }