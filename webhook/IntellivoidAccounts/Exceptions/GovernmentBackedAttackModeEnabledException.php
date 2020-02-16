<?php


    namespace IntellivoidAccounts\Exceptions;


    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class GovernmentBackedAttackModeEnabledException
     * @package IntellivoidAccounts\Exceptions
     */
    class GovernmentBackedAttackModeEnabledException extends Exception
    {
        /**
         * GovernmentBackedAttackModeEnabledException constructor.
         */
        public function __construct()
        {
            parent::__construct("The process cannot be completed because the account is currently being attacked by a government backed attack", ExceptionCodes::GovernmentBackedAttackModeEnabledException, null);
        }
    }