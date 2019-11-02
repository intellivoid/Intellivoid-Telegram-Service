<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class ApplicationStatus
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class ApplicationStatus
    {
        const Active = 0;

        const Disabled = 1;

        const Suspended = 3;
    }