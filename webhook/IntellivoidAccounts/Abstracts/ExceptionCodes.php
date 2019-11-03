<?php

    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class ExceptionCodes
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class ExceptionCodes
    {
        const InvalidUsernameException = 100;

        const InvalidEmailException = 101;

        const InvalidPasswordException = 102;

        const ConfigurationNotFoundException = 103;

        const DatabaseException = 104;

        const InvalidSearchMethodException = 105;

        const AccountNotFoundException = 106;

        const UsernameAlreadyExistsException = 107;

        const EmailAlreadyExistsException = 108;

        const IncorrectLoginDetailsException = 109;

        const AccountSuspendedException = 110;

        const InvalidIpException = 111;

        const InvalidLoginStatusException = 112;

        /** @deprecated */
        const BalanceTransactionRecordNotFoundException = 113;

        const AccountLimitedException = 114;

        const InvalidAccountStatusException = 115;

        /** @deprecated  */
        const InsufficientFundsException = 116;

        const InvalidVendorException = 117;

        /** @deprecated  */
        const InvalidTransactionTypeException = 118;

        /** @deprecated  */
        const TransactionRecordNotFoundException = 119;

        const HostNotKnownException = 120;

        const HostBlockedFromAccountException = 121;

        const InvalidMessageSubjectException = 122;

        const InvalidMessageContentException = 123;

        /** @deprecated */
        const TelegramClientNotFoundException = 124;

        const LoginRecordNotFoundException = 125;

        const InvalidArgumentException = 126;

        const InvalidRequestPermissionException = 127;

        const ApplicationNotFoundException = 128;

        const ApplicationAlreadyExistsException = 129;

        const AuthenticationRequestNotFoundException = 130;

        const AuthenticationAccessNotFoundException = 131;

        const AuthenticationRequestAlreadyUsedException = 132;

        const TelegramVerificationCodeNotFound = 133;

        const UserAgentNotFoundException = 134;

        const OtlNotFoundException = 135;

        const InvalidApplicationNameException = 136;

        const InvalidEventTypeException = 137;

        const ApplicationAccessNotFoundException = 138;

        const InvalidApplicationFlagException = 139;

        const TelegramApiException = 140;

        const TelegramServicesNotAvailableException = 141;

        const InvalidUrlException = 142;

        const TooManyPromptRequestsException = 143;

        const AuthNotPromptedException = 144;

        const AuthPromptExpiredException = 145;

        const AuthPromptAlreadyApprovedException = 146;

        const AuthPromptDeniedException = 147;
    }