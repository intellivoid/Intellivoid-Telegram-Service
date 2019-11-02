<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class AccountRequestPermissions
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class AccountRequestPermissions
    {
        /**
         * Access to personal information such as First Name, Last name, birthday, email if available
         */
        const ReadPersonalInformation = "READ_PERSONAL_INFORMATION";

        /**
         * Edits personal information
         */
        const EditPersonalInformation = "EDIT_PERSONAL_INFORMATION";

        /**
         * Views your Email Address
         */
        const ViewEmailAddress = "READ_EMAIL_ADDRESS";

        /**
         * Makes purchases or activate a paid subscription on the users behalf
         */
        const MakePurchases = "INVOKE_PURCHASES";

        /**
         * Send notifications to Telegram if available
         */
        const TelegramNotifications = "INVOKE_TELEGRAM_NOTIFICATIONS";
    }