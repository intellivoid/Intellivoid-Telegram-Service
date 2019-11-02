<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class TelegramVerificationCodeStatus
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class TelegramVerificationCodeStatus
    {
        const Active = 0;

        const Used = 1;

        const Unavailable = 2;
    }