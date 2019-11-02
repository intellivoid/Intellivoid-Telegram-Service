<?php

    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class LoginStatus
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class LoginStatus
    {
        const Unknown = 0;

        const Successful = 1;

        const IncorrectCredentials = 2;

        const VerificationFailed = 3;

        const UntrustedIpBlocked = 4;

        const BlockedSuspiciousActivities = 5;
    }