<?php


    namespace IntellivoidAccounts\Abstracts;


    /**
     * Class AuditEventType
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class AuditEventType
    {
        const NewLoginDetected = 0;

        const PasswordUpdated = 1;

        const PersonalInformationUpdated = 2;

        const EmailUpdated = 3;

        const MobileVerificationEnabled = 4;

        const MobileVerificationDisabled = 5;

         const RecoveryCodesEnabled = 6;

         const RecoveryCodesDisabled = 7;

         const TelegramVerificationEnabled = 8;

         const TelegramVerificationDisabled = 9;

         const ApplicationCreated = 10;

         const NewLoginLocationDetected = 11;
    }