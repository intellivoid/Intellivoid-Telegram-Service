<?php


    namespace IntellivoidAccounts\Abstracts\SearchMethods;

    /**
     * Class TelegramVerificationCodeSearchMethod
     * @package IntellivoidAccounts\Abstracts\SearchMethods
     */
    abstract class TelegramVerificationCodeSearchMethod
    {
        const byId = 'id';

        const byVerificationCode = 'verification_code';
    }