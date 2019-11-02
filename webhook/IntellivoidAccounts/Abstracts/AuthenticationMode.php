<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class AuthenticationMode
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class AuthenticationMode
    {
        /**
         * This option allows web applications to request authentication.
         * On success, it get's redirected back to the correct page to handle
         * the request.
         */
        const Redirect = 0;

        /**
         * This option redirects to /auth_success as a way for an application
         * to determine that this was a success. Same as Redirect
         */
        const ApplicationPlaceholder = 1;

        /**
         * This option gives the user a code to enter into the application's
         * prompt
         */
        const Code = 2;
    }